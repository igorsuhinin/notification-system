<?php

declare(strict_types=1);

namespace NotificationSystem\Domain\Notification\Contract;

use NotificationSystem\Domain\Notification\Entity\NotificationEntity;
use NotificationSystem\Domain\Notification\Exception\NotificationNotFoundException;
use Symfony\Component\Uid\Uuid;

interface NotificationRepositoryInterface
{
    final public const int MAX_LIMIT = 100;

    public function save(NotificationEntity $notification): void;

    public function findById(Uuid $id): ?NotificationEntity;

    /**
     * @throws NotificationNotFoundException when the notification with the given ID does not exist
     */
    public function get(Uuid $id): NotificationEntity;

    /**
     * @param positive-int $page
     * @param positive-int $limit
     * @param string $order Accepted values: 'ASC', 'DESC' (case-insensitive)
     *
     * @return list<NotificationEntity>
     */
    public function list(int $page = 1, int $limit = self::MAX_LIMIT, string $order = 'DESC'): array;

    /**
     * @return non-negative-int
     */
    public function getTotal(): int;
}
