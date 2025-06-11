<?php

declare(strict_types=1);

namespace NotificationSystem\Infrastructure\Notification\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry as ManagerRegistryInterface;
use InvalidArgumentException;
use NotificationSystem\Domain\Notification\Contract\NotificationRepositoryInterface;
use NotificationSystem\Domain\Notification\Entity\NotificationEntity;
use NotificationSystem\Domain\Notification\Exception\NotificationNotFoundException;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<NotificationEntity>
 */
final class DoctrineNotificationRepository extends ServiceEntityRepository implements NotificationRepositoryInterface
{
    public function __construct(ManagerRegistryInterface $registry)
    {
        parent::__construct($registry, NotificationEntity::class);
    }

    public function save(NotificationEntity $notification): void
    {
        $em = $this->getEntityManager();
        $em->persist($notification);
        $em->flush();
    }

    public function findById(Uuid $id): ?NotificationEntity
    {
        $entity = $this->find($id);

        assert($entity instanceof NotificationEntity || null === $entity);

        return $entity;
    }

    public function get(Uuid $id): NotificationEntity
    {
        $entity = $this->find($id);

        if (!$entity instanceof NotificationEntity) {
            throw new NotificationNotFoundException($id);
        }

        return $entity;
    }

    public function list(int $page = 1, int $limit = self::MAX_LIMIT, string $order = 'DESC'): array
    {
        if ($page < 1) {
            throw new InvalidArgumentException('Page number must be greater than 0');
        }

        if ($limit < 1 || $limit > self::MAX_LIMIT) {
            throw new InvalidArgumentException(sprintf('Limit must be between 1 and %d', self::MAX_LIMIT));
        }

        return $this->findBy(
            [],
            ['createdAt' => 'ASC' === strtoupper($order) ? 'ASC' : 'DESC'],
            min($limit, self::MAX_LIMIT),
            ($page - 1) * $limit
        );
    }

    public function getTotal(): int
    {
        return $this->count();
    }
}
