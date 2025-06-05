<?php

declare(strict_types=1);

namespace App\Notification\Domain\Event;

use App\Notification\Application\Contract\NotificationChannelSenderInterface as ChannelSenderInterface;
use Symfony\Component\Uid\Uuid;

final readonly class NotificationDeliverRequestedEvent
{
    public function __construct(public Uuid $id, public ChannelSenderInterface $channelSender)
    {
    }
}
