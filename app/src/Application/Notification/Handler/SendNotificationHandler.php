<?php

declare(strict_types=1);

namespace NotificationSystem\Application\Notification\Handler;

use NotificationSystem\Application\Notification\Command\SendNotificationCommand;
use NotificationSystem\Domain\Notification\Contract\NotificationRepositoryInterface;
use NotificationSystem\Domain\Notification\Event\NotificationCreateRequestedEvent;
use NotificationSystem\Domain\Notification\Event\NotificationSendRequestedEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(fromTransport: 'async')]
final readonly class SendNotificationHandler
{
    public function __construct(
        private NotificationRepositoryInterface $repository,
        private EventDispatcherInterface $eventDispatcher,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(SendNotificationCommand $command): void
    {
        $id = $command->notificationId;

        $notification = $this->repository->findById($id);
        if (!$notification) {
            $this->logger->debug('Notification not found; creating new one', ['notificationId' => $id]);

            $this->eventDispatcher->dispatch(new NotificationCreateRequestedEvent(
                notificationId: $id,
                to: $command->to,
                subject: $command->subject,
                content: $command->content,
                channel: $command->channel,
            ));
        }

        $this->eventDispatcher->dispatch(new NotificationSendRequestedEvent($id));
    }
}
