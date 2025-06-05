<?php

declare(strict_types=1);

namespace App\Notification\Domain\Exception;

use Symfony\Component\Uid\Uuid;

final class NotificationNotFoundException extends NotificationDomainException
{
    public function __construct(Uuid $notificationId)
    {
        parent::__construct("Notification with ID \"{$notificationId}\" not found");
    }
}
