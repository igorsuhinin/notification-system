<?php

declare(strict_types=1);

namespace App\Notification\Infrastructure\ChannelSender;

use App\Notification\Application\Channel\NotificationMessage;
use App\Notification\Application\Contract\NotificationChannelSenderInterface;
use App\Notification\Domain\Enum\ChannelTypeEnum;
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
