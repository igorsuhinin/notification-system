<?php

declare(strict_types=1);

namespace NotificationSystem\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Generator;
use Symfony\Component\Uid\Uuid;

class NotificationRandomFixture extends AbstractNotificationFixture
{
    public function load(ObjectManager $manager): void
    {
        foreach (self::generateRandomData() as $randomData) {
            $notification = $this->factory->create(
                id: Uuid::fromString($randomData['id']),
                to: $randomData['to'],
                subject: $randomData['subject'],
                content: $randomData['content'],
                channel: $randomData['channel']
            );

            if ($randomData['isSent']) {
                $notification->markAsSent();
            }
            if ($randomData['isDelivered']) {
                $notification->markAsDelivered();
            }
            if ($randomData['isFailed']) {
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
    public static function generateRandomData(int $totalItems = 100): Generator
    {
        $faker = FakerFactory::create();

        for ($i = 0; $i < $totalItems; $i++) {
            yield [
                'id' => Uuid::v4()->toRfc4122(),
                'to' => $faker->email,
                'subject' => $faker->sentence,
                'content' => $faker->paragraph,
                'channel' => $faker->randomElement(['email', 'sms', 'push']),
                'isSent' => $isSent = $faker->boolean(),
                'isDelivered' => $isDelivered = $faker->boolean(),
                'isFailed' => !$isSent && !$isDelivered,
            ];
        }
    }
}
