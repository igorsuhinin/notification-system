<?php

declare(strict_types=1);

namespace App\Notification\Application\Contract;

use App\Notification\Application\Channel\NotificationMessage;
use App\Notification\Domain\Enum\ChannelTypeEnum;

interface NotificationChannelSenderInterface
{
    public function send(NotificationMessage $message): void;

    public function isChannelSupported(ChannelTypeEnum $type): bool;
}
