<?php

declare(strict_types=1);

namespace App\Notification\Application\Exception;

use App\Notification\Domain\Enum\ChannelTypeEnum;
use App\Notification\Domain\Exception\NotificationDomainException;

final class NotificationChannelSenderNotFoundException extends NotificationDomainException
{
    public function __construct(ChannelTypeEnum $type)
    {
        parent::__construct("No channel sender found for channel type \"{$type->value}\"");
    }
}
