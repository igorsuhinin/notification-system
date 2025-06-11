<?php

declare(strict_types=1);

namespace NotificationSystem\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use NotificationSystem\Domain\Notification\Factory\NotificationFactory;

abstract class AbstractNotificationFixture extends Fixture
{
    public function __construct(protected readonly NotificationFactory $factory)
    {
    }
}
