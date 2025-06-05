<?php

declare(strict_types=1);

namespace App\Notification\Application\Listener;

use App\Notification\Application\Channel\NotificationMessage;
use App\Notification\Application\Contract\RecipientToProviderInterface;
use App\Notification\Application\Exception\NotificationChannelSenderFailedException;
use App\Notification\Domain\Contract\NotificationRepositoryInterface;
use App\Notification\Domain\Event\NotificationDeliverRequestedEvent;
use App\Notification\Domain\Event\NotificationFailedEvent;
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
