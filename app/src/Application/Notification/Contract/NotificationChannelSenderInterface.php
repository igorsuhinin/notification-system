<?php

declare(strict_types=1);

namespace NotificationSystem\Application\Notification\Contract;

use NotificationSystem\Application\Notification\Channel\NotificationMessage;
use NotificationSystem\Domain\Notification\Enum\ChannelTypeEnum;

interface NotificationChannelSenderInterface
{
    public function send(NotificationMessage $message): void;

    public function isChannelSupported(ChannelTypeEnum $type): bool;
}
