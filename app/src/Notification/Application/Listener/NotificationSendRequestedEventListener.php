<?php

declare(strict_types=1);

namespace App\Notification\Application\Listener;

use App\Notification\Application\Contract\NotificationChannelSenderInterface;
use App\Notification\Application\Exception\NotificationChannelSenderNotFoundException;
use App\Notification\Domain\Contract\NotificationRepositoryInterface;
use App\Notification\Domain\Event\NotificationDeliverRequestedEvent;
use App\Notification\Domain\Event\NotificationFailedEvent;
use App\Notification\Domain\Event\NotificationSendRequestedEvent;
use InvalidArgumentException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
final readonly class NotificationSendRequestedEventListener
{
    /**
     * @param iterable<NotificationChannelSenderInterface> $channels
     */
    public function __construct(
        private NotificationRepositoryInterface $repository,
        private EventDispatcherInterface $eventDispatcher,
        private LoggerInterface $logger,
        private iterable $channels,
    ) {
        $this->validateChannels($channels);
    }

    /**
     * @throws NotificationChannelSenderNotFoundException when no suitable channel sender is found
     */
    public function __invoke(NotificationSendRequestedEvent $event): void
    {
        $this->logger->info("Processing notification send request for ID \"{$event->id}\"", [
            'notificationId' => $event->id,
        ]);

        $notification = $this->repository->get($event->id);
        $notificationChannel = $notification->getChannel();

        foreach ($this->channels as $channel) {
            if (!$channel->isChannelSupported($notificationChannel)) {
                continue;
            }

            $this->logger->info(
                "Using channel \"{$notificationChannel->name}\" for notification ID \"{$event->id}\"",
                ['notificationId' => $event->id, 'channel' => $notificationChannel->value]
            );

            $this->eventDispatcher->dispatch(new NotificationDeliverRequestedEvent($event->id, $channel));

            return;
        }

        $this->logger->critical(
            'No suitable notification channel sender found',
            ['notificationId' => $event->id, 'channel' => $notificationChannel->value]
        );

        $this->eventDispatcher->dispatch(new NotificationFailedEvent($event->id));

        throw new NotificationChannelSenderNotFoundException($notificationChannel);
    }

    /**
     * @param iterable<NotificationChannelSenderInterface> $channels
     */
    private function validateChannels(iterable $channels): void
    {
        foreach ($channels as $channel) {
            if (!$channel instanceof NotificationChannelSenderInterface) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Channel "%s" must implement NotificationChannelInterface',
                        get_class($channel)
                    )
                );
            }
        }
    }
}
