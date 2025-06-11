<?php

declare(strict_types=1);

namespace NotificationSystem\UI\Notification\Response;

use InvalidArgumentException;
use JsonSerializable;
use OpenApi\Attributes as OA;
use Override;

#[OA\Schema(
    description: 'Paginated response wrapper',
    required: ['total', 'page', 'limit', 'items']
)]
final readonly class PaginationResponse implements JsonSerializable
{
    /**
     * @param non-negative-int $total
     * @param non-negative-int $page
     * @param positive-int $limit
     * @param list<NotificationResponse> $items
     */
    public function __construct(
        #[OA\Property(description: 'Total number of notifications', type: 'integer')]
        public int $total,
        #[OA\Property(description: 'Current page number', type: 'integer')]
        public int $page,
        #[OA\Property(description: 'Items per page', type: 'integer')]
        public int $limit,
        #[OA\Property(
            description: 'List of notifications',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/NotificationResponse')
        )]
        public array $items,
    ) {
        $this->validateItems($items);
    }

    /**
     * @return array{
     *     total: non-negative-int,
     *     page: non-negative-int,
     *     limit: positive-int,
     *     items: list<NotificationResponse>
     * }
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'total' => $this->total,
            'page' => $this->page,
            'limit' => $this->limit,
            'items' => $this->items,
        ];
    }

    /**
     * @param list<NotificationResponse> $items
     */
    private function validateItems(array $items): void
    {
        foreach ($items as $item) {
            if (!$item instanceof NotificationResponse) {
                throw new InvalidArgumentException('All items must be instances of NotificationResponse');
            }
        }
    }
}
