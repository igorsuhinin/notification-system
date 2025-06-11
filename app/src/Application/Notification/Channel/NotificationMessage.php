<?php

declare(strict_types=1);

namespace NotificationSystem\Application\Notification\Channel;

use Symfony\Component\Uid\Uuid;

final readonly class NotificationMessage
{
    /**
     * @param non-empty-string $to
     * @param non-empty-string $subject
     * @param non-empty-string $content
     */
    public function __construct(
        public Uuid $notificationId,
        public string $to,
        public string $subject,
        public string $content,
    ) {
    }
}
