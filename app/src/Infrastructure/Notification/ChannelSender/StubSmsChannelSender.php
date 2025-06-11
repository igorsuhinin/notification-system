<?php

declare(strict_types=1);

namespace NotificationSystem\Infrastructure\Notification\ChannelSender;

use NotificationSystem\Application\Notification\Channel\NotificationMessage;
use NotificationSystem\Domain\Notification\Enum\ChannelTypeEnum;
use NotificationSystem\Domain\Notification\Event\NotificationDeliveredEvent;
use NotificationSystem\Domain\Notification\Event\NotificationSentEvent;
use Override;

/**
 * StubSmsChannel is a mock implementation of an SMS channel for testing purposes.
 */
final readonly class StubSmsChannelSender extends AbstractChannelSender
{
    #[Override]
    protected function processSending(NotificationMessage $message): void
    {
        // We could process the SMS sending asynchronously but for the simplicity we'll just log it here
        $this->logger->info("[StubSMS] To: {$message->to}, Body: {$message->content}");

        // Dispatch an event to indicate that the notification has been sent
        $this->eventDispatcher->dispatch(new NotificationSentEvent($message->notificationId));

        // Some logic to simulate SMS delivery could go here...

        // Let's assume the SMS is sent successfully so we dispatch the delivered event
        $this->eventDispatcher->dispatch(new NotificationDeliveredEvent($message->notificationId));
    }

    #[Override]
    protected function getSupportedChannels(): array
    {
        return [ChannelTypeEnum::SMS];
    }
}
