<?php

declare(strict_types=1);

namespace App\Notification\Domain\Enum;

enum ChannelTypeEnum: string
{
    case EMAIL = 'email';
    case SMS = 'sms';
    case PUSH = 'push';
}
