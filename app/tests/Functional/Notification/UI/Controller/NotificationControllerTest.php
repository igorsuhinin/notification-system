<?php

declare(strict_types=1);

namespace App\Tests\Functional\Notification\UI\Controller;

use App\DataFixtures\NotificationFixedFixture;
use App\DataFixtures\NotificationRandomFixture;
use App\Notification\Domain\Contract\NotificationRepositoryInterface;
use App\Notification\Domain\Enum\NotificationStatusEnum;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;
use App\Notification\Application\Contract\RecipientToProviderInterface;

final class NotificationControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;
    private AbstractDatabaseTool $databaseTool;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $container = static::getContainer();

        $this->entityManager = $container->get(EntityManagerInterface::class);
        assert($this->entityManager instanceof EntityManagerInterface);

        $dbToolCollection = $container->get(DatabaseToolCollection::class);
        assert($dbToolCollection instanceof DatabaseToolCollection);

        $this->databaseTool = $dbToolCollection->get();

        $purger = new ORMPurger($this->entityManager);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        new ORMExecutor($this->entityManager, $purger)->purge();
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();

        parent::tearDown();
    }

    #[DataProvider('getCorrectPayloadData')]
    public function testSendNotificationSuccess(array $payload, NotificationStatusEnum $finalStatus): void
    {
        $this->client->request('POST', '/api/notifications', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($payload));

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_ACCEPTED, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertTrue(Uuid::isValid($data['id']));

        $container = static::getContainer();

        // Check that the notification was created in the database with the correct data
        $repository = $container->get(NotificationRepositoryInterface::class);
        assert($repository instanceof NotificationRepositoryInterface);

        $notification = $repository->get(Uuid::fromString($data['id']));

        $recipientToProvider = $container->get(RecipientToProviderInterface::class);
        assert($recipientToProvider instanceof RecipientToProviderInterface);

        $this->assertNotNull($notification);
        $to = $recipientToProvider->get($notification->getRecipient(), $notification->getChannel());
        $this->assertEquals($payload['to'], $to);
        $this->assertEquals($payload['subject'], $notification->getSubject()->getValue());
        $this->assertEquals($payload['content'], $notification->getBody()->getValue());
        $this->assertEquals($payload['channel'], $notification->getChannel()->value);
        $this->assertEquals($finalStatus->value, $notification->getStatus()->value);
        $this->assertNotNull($notification->getCreatedAt());
        $this->assertNotNull($notification->getLastUpdatedAt());

        if ($finalStatus === NotificationStatusEnum::FAILED) {
            $this->assertNull($notification->getSentAt());
            $this->assertNull($notification->getDeliveredAt());
            $this->assertNotNull($notification->getFailedAt());
        } else {
            $this->assertNotNull($notification->getSentAt());
            $this->assertNotNull($notification->getDeliveredAt());
            $this->assertNull($notification->getFailedAt());
        }
    }

    public static function getCorrectPayloadData(): array
    {
        return [
            'email' => [
                'payload' => [
                    'to' => 'test@test.com',
                    'subject' => 'Test Subject',
                    'content' => 'Test Content',
                    'channel' => 'email',
                ],
                'finalStatus' => NotificationStatusEnum::DELIVERED,
            ],
            'sms' => [
                'payload' => [
                    'to' => '+1234567890',
                    'subject' => 'Test Subject',
                    'content' => 'Test Content',
                    'channel' => 'sms',
                ],
                'finalStatus' => NotificationStatusEnum::DELIVERED,
            ],
            'push' => [
                'payload' => [
                    'to' => 'token123',
                    'subject' => 'Test Subject',
                    'content' => 'Test Content',
                    'channel' => 'push',
                ],
                'finalStatus' => NotificationStatusEnum::FAILED, // No push provider configured, so it must fail
            ],
        ];
    }

    #[DataProvider('getIncorrectPayloadData')]
    public function testSendNotificationValidationFailed(array $payload): void
    {
        $this->client->request('POST', '/api/notifications', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($payload));

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());
    }

    public static function getIncorrectPayloadData(): array
    {
        return [
            'email-incorrect' => [[
                'to' => 'incorrect-email',
                'subject' => 'Test Subject',
                'content' => 'Test Content',
                'channel' => 'email',
            ]],
            'phone-incorrect' => [[
                'to' => '+1234567890abc',
                'subject' => 'Test Subject',
                'content' => 'Test Content',
                'channel' => 'sms',
            ]],
            'phone-too-short' => [[
                'to' => '+12345',
                'subject' => 'Test Subject',
                'content' => 'Test Content',
                'channel' => 'sms',
            ]],
            'phone-too-long' => [[
                'to' => '+12345678901234567890',
                'subject' => 'Test Subject',
                'content' => 'Test Content',
                'channel' => 'sms',
            ]],
            'token-too-short' => [[
                'to' => 'toke',
                'subject' => 'Test Subject',
                'content' => 'Test Content',
                'channel' => 'push',
            ]],
            'token-too-long' => [[
                'to' => 'thisisapushnotificationtokenthatshouldnotbeacceptedthisisapushnotificationtokenthatshouldnotbeaccepted',
                'subject' => 'Test Subject',
                'content' => 'Test Content',
                'channel' => 'push',
            ]],
            'empty-subject' => [[
                'to' => 'test@test.com',
                'subject' => '',
                'content' => 'Test Content',
                'channel' => 'email',
            ]],
            'empty-content' => [[
                'to' => 'test@test.com',
                'subject' => 'Test Subject',
                'content' => '',
                'channel' => 'email',
            ]],
            'empty-channel' => [[
                'to' => 'test@test.com',
                'subject' => 'Test Subject',
                'content' => 'Test Content',
                'channel' => '',
            ]],
            'incorrect-channel' => [[
                'to' => 'test@test.com',
                'subject' => 'Test Subject',
                'content' => 'Test Content',
                'channel' => 'invalid_channel',
            ]],
            'to-field-missing' => [[
                'subject' => 'Test Subject',
                'content' => 'Test Content',
                'channel' => 'email',
            ]],
            'subject-field-missing' => [[
                'to' => 'incorrect-email',
                'content' => 'Test Content',
                'channel' => 'email',
            ]],
            'content-field-missing' => [[
                'to' => 'incorrect-email',
                'subject' => 'Test Subject',
                'channel' => 'email',
            ]],
            'channel-field-missing' => [[
                'to' => 'incorrect-email',
                'subject' => 'Test Subject',
                'content' => 'Test Content',
            ]],
        ];
    }

    public function testSendNotificationWithInvalidPayload(): void
    {
        $this->client->request('POST', '/api/notifications', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], '{invalid json');

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function testListNotifications(): void
    {
        $this->databaseTool->loadFixtures([NotificationRandomFixture::class]);

        $this->client->request('GET', '/api/notifications');

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('total', $data);
        $this->assertEquals(100, $data['total']);
        $this->assertArrayHasKey('page', $data);
        $this->assertEquals(1, $data['page']);
        $this->assertArrayHasKey('limit', $data);
        $this->assertEquals(20, $data['limit']);
        $this->assertArrayHasKey('items', $data);
        $this->assertIsArray($data['items']);
        $this->assertCount(20, $data['items']);
        foreach ($data['items'] as $item) {
            $this->assertArrayHasKey('id', $item);
            $this->assertArrayHasKey('channel', $item);
            $this->assertArrayHasKey('status', $item);
            $this->assertArrayHasKey('createdAt', $item);
            $this->assertArrayHasKey('updatedAt', $item);
            $this->assertTrue(Uuid::isValid($item['id']));
        }

        $this->client->request('GET', '/api/notifications?page=2&limit=5&order=asc');

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('total', $data);
        $this->assertEquals(100, $data['total']);
        $this->assertArrayHasKey('page', $data);
        $this->assertEquals(2, $data['page']);
        $this->assertArrayHasKey('limit', $data);
        $this->assertEquals(5, $data['limit']);
        $this->assertArrayHasKey('items', $data);
        $this->assertIsArray($data['items']);
        $this->assertCount(5, $data['items']);
    }

    public function testShowNotificationSuccess(): void
    {
        $this->databaseTool->loadFixtures([NotificationFixedFixture::class]);

        $uuid = NotificationFixedFixture::NOTIFICATION_ID_1;
        $this->client->request('GET', "/api/notifications/{$uuid}");

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('id', $data);
        $this->assertEquals($uuid, $data['id']);
        $this->assertArrayHasKey('channel', $data);
        $this->assertEquals(NotificationFixedFixture::NOTIFICATION_CHANNEL_1, $data['channel']);
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('sent', $data['status']);
        $this->assertArrayHasKey('createdAt', $data);
        $this->assertArrayHasKey('updatedAt', $data);
    }

    public function testShowNotificationNotFound(): void
    {
        $this->client->request('GET', '/api/notifications/' . Uuid::v4());

        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testShowNotificationInvalidUuid(): void
    {
        $this->client->request('GET', '/api/notifications/invalid-uuid');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }
}
