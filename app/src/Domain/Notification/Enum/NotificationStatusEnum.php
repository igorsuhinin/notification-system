<?php

declare(strict_types=1);

namespace NotificationSystem\Domain\Notification\Enum;

enum NotificationStatusEnum: string
{
    case QUEUED = 'queued';
    case SENT = 'sent';
    case FAILED = 'failed';
    case DELIVERED = 'delivered';
}
