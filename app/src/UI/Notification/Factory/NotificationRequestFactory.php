<?php

declare(strict_types=1);

namespace NotificationSystem\UI\Notification\Factory;

use NotificationSystem\UI\Notification\Request\NotificationRequest;

final readonly class NotificationRequestFactory
{
    /**
     * @param array<array-key, mixed> $data
     *
     * @psalm-suppress MixedArgument
     * @psalm-suppress PossiblyInvalidArgument
     */
    public function makeFromRequestData(array $data): NotificationRequest
    {
        return new NotificationRequest(
            channel: $data['channel'] ?? '',
            content: $data['content'] ?? '',
            subject: $data['subject'] ?? '',
            to: $data['to'] ?? ''
        );
    }
}
