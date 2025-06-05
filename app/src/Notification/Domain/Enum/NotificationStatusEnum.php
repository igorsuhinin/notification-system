<?php

declare(strict_types=1);

namespace App\Notification\Domain\Enum;

enum NotificationStatusEnum: string
{
    case QUEUED = 'queued';
    case SENT = 'sent';
    case FAILED = 'failed';
    case DELIVERED = 'delivered';
}
