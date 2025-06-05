<?php

declare(strict_types=1);

namespace App\Notification\Infrastructure\ChannelSender;

use App\Notification\Application\Channel\NotificationMessage;
use App\Notification\Domain\Enum\ChannelTypeEnum;
use App\Notification\Domain\Event\NotificationDeliveredEvent;
use App\Notification\Domain\Event\NotificationSentEvent;
use Override;

/**
 * StubEmailChannel is a mock implementation of an email channel for testing purposes.
 */
final readonly class StubEmailChannelSender extends AbstractChannelSender
{
    #[Override]
    protected function processSending(NotificationMessage $message): void
    {
        // We could process the email sending asynchronously but for the simplicity we'll just log it here
        $this->logger->info("[StubEmail] To: {$message->to}, Subject: {$message->subject}, Body: {$message->content}");

        // Dispatch an event to indicate that the notification has been sent
        $this->eventDispatcher->dispatch(new NotificationSentEvent($message->notificationId));

        // Some logic to simulate email delivery could go here...

        // Let's assume the email is delivered successfully
        $this->eventDispatcher->dispatch(new NotificationDeliveredEvent($message->notificationId));
    }

    #[Override]
    protected function getSupportedChannels(): array
    {
        return [ChannelTypeEnum::EMAIL];
    }
}
