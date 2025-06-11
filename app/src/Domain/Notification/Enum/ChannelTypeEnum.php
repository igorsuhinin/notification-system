<?php

declare(strict_types=1);

namespace NotificationSystem\Domain\Notification\Enum;

enum ChannelTypeEnum: string
{
    case EMAIL = 'email';
    case SMS = 'sms';
    case PUSH = 'push';
}
