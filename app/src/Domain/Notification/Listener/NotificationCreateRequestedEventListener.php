<?php

declare(strict_types=1);

namespace NotificationSystem\Domain\Notification\Listener;

use NotificationSystem\Domain\Notification\Contract\NotificationRepositoryInterface;
use NotificationSystem\Domain\Notification\Event\NotificationCreatedEvent;
use NotificationSystem\Domain\Notification\Event\NotificationCreateRequestedEvent;
use NotificationSystem\Domain\Notification\Factory\NotificationFactory;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
final readonly class NotificationCreateRequestedEventListener
{
    public function __construct(
        private NotificationRepositoryInterface $repository,
        private NotificationFactory $factory,
        private EventDispatcherInterface $eventDispatcher,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(NotificationCreateRequestedEvent $event): void
    {
        $notification = $this->factory->create(
            id: $event->notificationId,
            to: $event->to,
            subject: $event->subject,
            content: $event->content,
            channel: $event->channel
        );

        $this->repository->save($notification);

        $this->logger->info('Notification created', ['notificationId' => $notification->getId()]);

        $this->eventDispatcher->dispatch(new NotificationCreatedEvent($notification->getId()));
    }
}
