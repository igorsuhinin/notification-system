<?php

declare(strict_types=1);

namespace App\Notification\Domain\Listener;

use App\Notification\Domain\Contract\NotificationRepositoryInterface;
use App\Notification\Domain\Event\NotificationDeliveredEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
final readonly class NotificationDeliveredEventListener
{
    public function __construct(private NotificationRepositoryInterface $repository)
    {
    }

    public function __invoke(NotificationDeliveredEvent $event): void
    {
        $notification = $this->repository->get($event->id);

        $notification->markAsDelivered();

        $this->repository->save($notification);
    }
}
