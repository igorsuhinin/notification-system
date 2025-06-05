<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use Generator;
use Symfony\Component\Uid\Uuid;

class NotificationFixedFixture extends AbstractNotificationFixture
{
    public const string NOTIFICATION_ID_1 = '00000000-0000-0000-0000-000000000001';
    public const string NOTIFICATION_TO_1 = 'test@test.com';
    public const string NOTIFICATION_SUBJECT_1 = 'Test Subject 1';
    public const string NOTIFICATION_CONTENT_1 = 'Test Content 1';
    public const string NOTIFICATION_CHANNEL_1 = 'email';
    public const bool NOTIFICATION_IS_SENT_1 = true;
    public const bool NOTIFICATION_IS_DELIVERED_1 = false;
    public const bool NOTIFICATION_IS_FAILED_1 = false;

    public const string NOTIFICATION_ID_2 = '00000000-0000-0000-0000-000000000002';
    public const string NOTIFICATION_TO_2 = '+1234567890';
    public const string NOTIFICATION_SUBJECT_2 = 'Test Subject 2';
    public const string NOTIFICATION_CONTENT_2 = 'Test Content 2';
    public const string NOTIFICATION_CHANNEL_2 = 'sms';
    public const bool NOTIFICATION_IS_SENT_2 = false;
    public const bool NOTIFICATION_IS_DELIVERED_2 = true;
    public const bool NOTIFICATION_IS_FAILED_2 = false;

    public const string NOTIFICATION_ID_3 = '00000000-0000-0000-0000-000000000003';
    public const string NOTIFICATION_TO_3 = 'token123';
    public const string NOTIFICATION_SUBJECT_3 = 'Test Subject 3';
    public const string NOTIFICATION_CONTENT_3 = 'Test Content 3';
    public const string NOTIFICATION_CHANNEL_3 = 'push';
    public const bool NOTIFICATION_IS_SENT_3 = false;
    public const bool NOTIFICATION_IS_DELIVERED_3 = false;
    public const bool NOTIFICATION_IS_FAILED_3 = true;

    public function load(ObjectManager $manager): void
    {
        foreach (self::getData() as $data) {
            $notification = $this->factory->create(
                id: Uuid::fromString($data['id']),
                to: $data['to'],
                subject: $data['subject'],
                content: $data['content'],
                channel: $data['channel']
            );

            if ($data['isSent']) {
                $notification->markAsSent();
            }
            if ($data['isDelivered']) {
                $notification->markAsDelivered();
            }
            if ($data['isFailed']) {
                $notification->markAsFailed();
            }

            $manager->persist($notification);
        }

        $manager->flush();
        $manager->clear();
    }

    /**
     * @return Generator<int, array{
     *     id: non-empty-string,
     *     to: non-empty-string,
     *     subject: non-empty-string,
     *     content: non-empty-string,
     *     channel: non-empty-string,
     *     isSent: bool,
     *     isDelivered: bool,
     *     isFailed: bool,
     * }>
     */
    public static function getData(): Generator
    {
        yield [
            'id' => self::NOTIFICATION_ID_1,
            'to' => self::NOTIFICATION_TO_1,
            'subject' => self::NOTIFICATION_SUBJECT_1,
            'content' => self::NOTIFICATION_CONTENT_1,
            'channel' => self::NOTIFICATION_CHANNEL_1,
            'isSent' => self::NOTIFICATION_IS_SENT_1,
            'isDelivered' => self::NOTIFICATION_IS_DELIVERED_1,
            'isFailed' => self::NOTIFICATION_IS_FAILED_1,
        ];
        yield [
            'id' => self::NOTIFICATION_ID_2,
            'to' => self::NOTIFICATION_TO_2,
            'subject' => self::NOTIFICATION_SUBJECT_2,
            'content' => self::NOTIFICATION_CONTENT_2,
            'channel' => self::NOTIFICATION_CHANNEL_2,
            'isSent' => self::NOTIFICATION_IS_SENT_2,
            'isDelivered' => self::NOTIFICATION_IS_DELIVERED_2,
            'isFailed' => self::NOTIFICATION_IS_FAILED_2,
        ];
        yield [
            'id' => self::NOTIFICATION_ID_3,
            'to' => self::NOTIFICATION_TO_3,
            'subject' => self::NOTIFICATION_SUBJECT_3,
            'content' => self::NOTIFICATION_CONTENT_3,
            'channel' => self::NOTIFICATION_CHANNEL_3,
            'isSent' => self::NOTIFICATION_IS_SENT_3,
            'isDelivered' => self::NOTIFICATION_IS_DELIVERED_3,
            'isFailed' => self::NOTIFICATION_IS_FAILED_3,
        ];
    }
}
