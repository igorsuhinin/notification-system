<?php

declare(strict_types=1);

namespace NotificationSystem\Application\Notification\Command;

use Symfony\Component\Uid\Uuid;

final readonly class SendNotificationCommand
{
    /**
     * @param non-empty-string $to
     * @param non-empty-string $subject
     * @param non-empty-string $content
     * @param non-empty-string $channel
     */
    public function __construct(
        public Uuid $notificationId,
        public string $to,
        public string $subject,
        public string $content,
        public string $channel,
    ) {
    }
}
