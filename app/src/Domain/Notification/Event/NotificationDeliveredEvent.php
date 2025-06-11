<?php

declare(strict_types=1);

namespace NotificationSystem\Domain\Notification\Event;

use Symfony\Component\Uid\Uuid;

final readonly class NotificationDeliveredEvent
{
    public function __construct(public Uuid $id)
    {
    }
}
