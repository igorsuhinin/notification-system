<?php

declare(strict_types=1);

namespace NotificationSystem\Application\Notification\Listener;

use NotificationSystem\Application\Notification\Channel\NotificationMessage;
use NotificationSystem\Application\Notification\Contract\RecipientToProviderInterface;
use NotificationSystem\Application\Notification\Exception\NotificationChannelSenderFailedException;
use NotificationSystem\Domain\Notification\Contract\NotificationRepositoryInterface;
use NotificationSystem\Domain\Notification\Event\NotificationDeliverRequestedEvent;
use NotificationSystem\Domain\Notification\Event\NotificationFailedEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Throwable;

#[AsEventListener]
final readonly class NotificationDeliverRequestedEventListener
{
    public function __construct(
        private NotificationRepositoryInterface $repository,
        private RecipientToProviderInterface $recipientToProvider,
        private EventDispatcherInterface $eventDispatcher,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws NotificationChannelSenderFailedException when the notification cannot be sent
     */
    public function __invoke(NotificationDeliverRequestedEvent $event): void
    {
        $notification = $this->repository->get($event->id);
        $notificationChannel = $notification->getChannel();

        $to = $this->recipientToProvider->get($notification->getRecipient(), $notificationChannel);
        $subject = $notification->getSubject()->getValue();
        $body = $notification->getBody()->getValue();

        try {
            $event->channelSender->send(new NotificationMessage($event->id, $to, $subject, $body));
        } catch (Throwable $e) {
            $this->logger->error(
                'Failed to send notification',
                [
                    'notificationId' => $event->id,
                    'channel' => $notificationChannel->value,
                    'error' => $e->getMessage(),
                ]
            );

            $this->eventDispatcher->dispatch(new NotificationFailedEvent($event->id));

            throw new NotificationChannelSenderFailedException(
                notificationId: $event->id,
                channel: $notificationChannel,
                previous: $e
            );
        }
    }
}
