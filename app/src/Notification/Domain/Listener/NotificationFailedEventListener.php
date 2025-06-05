<?php

declare(strict_types=1);

namespace App\Notification\Domain\Listener;

use App\Notification\Domain\Contract\NotificationRepositoryInterface;
use App\Notification\Domain\Event\NotificationFailedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
final readonly class NotificationFailedEventListener
{
    public function __construct(private NotificationRepositoryInterface $repository)
    {
    }

    public function __invoke(NotificationFailedEvent $event): void
    {
        $notification = $this->repository->get($event->id);

        $notification->markAsFailed();

        $this->repository->save($notification);
    }
}
