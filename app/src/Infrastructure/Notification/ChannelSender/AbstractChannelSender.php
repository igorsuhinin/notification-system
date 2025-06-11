<?php

declare(strict_types=1);

namespace NotificationSystem\Infrastructure\Notification\ChannelSender;

use NotificationSystem\Application\Notification\Channel\NotificationMessage;
use NotificationSystem\Application\Notification\Contract\NotificationChannelSenderInterface;
use NotificationSystem\Domain\Notification\Enum\ChannelTypeEnum;
use Override;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

abstract readonly class AbstractChannelSender implements NotificationChannelSenderInterface
{
    public function __construct(
        protected EventDispatcherInterface $eventDispatcher,
        protected LoggerInterface $logger,
    ) {
    }

    #[Override]
    final public function send(NotificationMessage $message): void
    {
        $this->processSending($message);
    }

    #[Override]
    final public function isChannelSupported(ChannelTypeEnum $type): bool
    {
        return in_array($type, $this->getSupportedChannels(), true);
    }

    abstract protected function processSending(NotificationMessage $message): void;

    /**
     * @return non-empty-list<ChannelTypeEnum>
     */
    abstract protected function getSupportedChannels(): array;
}
