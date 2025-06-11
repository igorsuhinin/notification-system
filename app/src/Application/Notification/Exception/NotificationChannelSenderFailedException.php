<?php

declare(strict_types=1);

namespace NotificationSystem\Application\Notification\Exception;

use NotificationSystem\Domain\Notification\Enum\ChannelTypeEnum;
use NotificationSystem\Domain\Notification\Exception\NotificationDomainException;
use Symfony\Component\Uid\Uuid;
use Throwable;

final class NotificationChannelSenderFailedException extends NotificationDomainException
{
    public function __construct(Uuid $notificationId, ChannelTypeEnum $channel, ?Throwable $previous = null)
    {
        parent::__construct(
            "Failed to send notification with ID \"{$notificationId}\" via channel \"{$channel->value}\"",
            previous: $previous
        );
    }
}
