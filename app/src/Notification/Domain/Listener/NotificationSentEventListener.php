<?php

declare(strict_types=1);

namespace App\Notification\Domain\Listener;

use App\Notification\Domain\Contract\NotificationRepositoryInterface;
use App\Notification\Domain\Event\NotificationSentEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
final readonly class NotificationSentEventListener
{
    public function __construct(private NotificationRepositoryInterface $repository)
    {
    }

    public function __invoke(NotificationSentEvent $event): void
    {
        $notification = $this->repository->get($event->id);

        $notification->markAsSent();

        $this->repository->save($notification);
    }
}
