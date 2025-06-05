<?php

declare(strict_types=1);

namespace App\Notification\UI\Factory;

use App\Notification\Domain\Entity\NotificationEntity;
use App\Notification\UI\Response\NotificationResponse;

final class NotificationResponseFactory
{
    public function makeFromEntity(NotificationEntity $entity): NotificationResponse
    {
        /** @psalm-suppress ArgumentTypeCoercion We expect that toRfc4122() method returns a non-empty string */
        return new NotificationResponse(
            /* @phpstan-ignore-next-line Same as above */
            id: $entity->getId()->toRfc4122(),
            channel: $entity->getChannel()->value,
            status: $entity->getStatus()->value,
            createdAt: $entity->getCreatedAt()->format(DATE_ATOM),
            updatedAt: $entity->getLastUpdatedAt()?->format(DATE_ATOM),
        );
    }

    /**
     * @param list<NotificationEntity> $collection
     *
     * @return list<NotificationResponse>
     */
    public function makeFromCollection(array $collection): array
    {
        return array_map(
            fn (NotificationEntity $entity): NotificationResponse => $this->makeFromEntity($entity),
            $collection
        );
    }
}
