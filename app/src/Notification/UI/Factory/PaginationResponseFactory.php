<?php

declare(strict_types=1);

namespace App\Notification\UI\Factory;

use App\Notification\Domain\Entity\NotificationEntity;
use App\Notification\UI\Response\PaginationResponse;

final readonly class PaginationResponseFactory
{
    public function __construct(private NotificationResponseFactory $notificationFactory)
    {
    }

    /**
     * @param non-negative-int $total
     * @param non-negative-int $page
     * @param positive-int $limit
     * @param list<NotificationEntity> $entities
     */
    public function create(int $total, int $page, int $limit, array $entities): PaginationResponse
    {
        return new PaginationResponse(
            total: $total,
            page: $page,
            limit: $limit,
            items: $this->notificationFactory->makeFromCollection($entities)
        );
    }
}
