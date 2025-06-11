<?php

declare(strict_types=1);

namespace NotificationSystem\Domain\Notification\Event;

use NotificationSystem\Application\Notification\Contract\NotificationChannelSenderInterface as ChannelSenderInterface;
use Symfony\Component\Uid\Uuid;

final readonly class NotificationDeliverRequestedEvent
{
    public function __construct(public Uuid $id, public ChannelSenderInterface $channelSender)
    {
    }
}
