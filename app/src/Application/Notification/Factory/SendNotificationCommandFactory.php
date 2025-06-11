<?php

declare(strict_types=1);

namespace NotificationSystem\Application\Notification\Factory;

use NotificationSystem\Application\Notification\Command\SendNotificationCommand;
use NotificationSystem\UI\Notification\Request\NotificationRequest;
use Symfony\Component\Uid\Uuid;

final class SendNotificationCommandFactory
{
    public function createFromNotificationRequest(NotificationRequest $request): SendNotificationCommand
    {
        return new SendNotificationCommand(
            notificationId: Uuid::v4(),
            to: $request->to,
            subject: $request->subject,
            content: $request->content,
            channel: $request->channel
        );
    }
}
