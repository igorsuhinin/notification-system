<?php

declare(strict_types=1);

namespace NotificationSystem\Domain\Notification\Listener;

use NotificationSystem\Domain\Notification\Contract\NotificationRepositoryInterface;
use NotificationSystem\Domain\Notification\Event\NotificationDeliveredEvent;
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
