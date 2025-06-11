<?php

declare(strict_types=1);

namespace NotificationSystem\Application\Notification\Exception;

use NotificationSystem\Domain\Notification\Enum\ChannelTypeEnum;
use NotificationSystem\Domain\Notification\Exception\NotificationDomainException;

final class NotificationChannelSenderNotFoundException extends NotificationDomainException
{
    public function __construct(ChannelTypeEnum $type)
    {
        parent::__construct("No channel sender found for channel type \"{$type->value}\"");
    }
}
