<?php

declare(strict_types=1);

namespace App\Notification\UI\Factory;

use App\Notification\UI\Request\NotificationRequest;

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
