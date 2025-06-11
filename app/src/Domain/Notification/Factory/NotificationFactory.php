<?php

declare(strict_types=1);

namespace NotificationSystem\Domain\Notification\Factory;

use NotificationSystem\Domain\Notification\Entity\NotificationEntity;
use NotificationSystem\Domain\Notification\Enum\ChannelTypeEnum;
use NotificationSystem\Domain\Notification\ValueObject\MessageBody;
use NotificationSystem\Domain\Notification\ValueObject\MessageSubject;
use Symfony\Component\Uid\Uuid;

final readonly class NotificationFactory
{
    public function __construct(private RecipientFactory $recipientFactory)
    {
    }

    /**
     * @param non-empty-string $to
     * @param non-empty-string $subject
     * @param non-empty-string $content
     * @param non-empty-string $channel
     */
    public function create(Uuid $id, string $to, string $subject, string $content, string $channel): NotificationEntity
    {
        $channelType = ChannelTypeEnum::from($channel);

        return new NotificationEntity(
            $id,
            $this->recipientFactory->create($channelType, $to),
            $channelType,
            new MessageSubject($subject),
            new MessageBody($content)
        );
    }
}
