<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Notification\Domain\Factory\NotificationFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;

abstract class AbstractNotificationFixture extends Fixture
{
    public function __construct(protected readonly NotificationFactory $factory)
    {
    }
}
