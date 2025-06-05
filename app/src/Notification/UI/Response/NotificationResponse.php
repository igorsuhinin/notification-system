<?php

declare(strict_types=1);

namespace App\Notification\UI\Response;

use JsonSerializable;
use OpenApi\Attributes as OA;
use Override;

#[OA\Schema(
    description: 'Notification response payload',
    required: ['id', 'channel', 'status', 'createdAt']
)]
final readonly class NotificationResponse implements JsonSerializable
{
    /**
     * @param non-empty-string $id
     * @param non-empty-string $channel
     * @param non-empty-string $status
     * @param non-empty-string $createdAt
     * @param non-empty-string|null $updatedAt
     */
    public function __construct(
        #[OA\Property(description: 'UUID of the notification', type: 'string', format: 'uuid')]
        public string $id,
        #[OA\Property(description: 'Notification channel', type: 'string', enum: ['email', 'sms', 'push'])]
        public string $channel,
        #[OA\Property(description: 'Notification delivery status', type: 'string')]
        public string $status,
        #[OA\Property(description: 'Creation timestamp (ISO 8601)', type: 'string', format: 'date-time')]
        public string $createdAt,
        #[OA\Property(
            description: 'Last update timestamp (ISO 8601)',
            type: 'string',
            format: 'date-time',
            nullable: true
        )]
        public ?string $updatedAt = null,
    ) {
    }

    /**
     * @return array{
     *     id: non-empty-string,
     *     channel: non-empty-string,
     *     status: non-empty-string,
     *     createdAt: non-empty-string,
     *     updatedAt?: non-empty-string|null,
     * }
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'channel' => $this->channel,
            'status' => $this->status,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}
