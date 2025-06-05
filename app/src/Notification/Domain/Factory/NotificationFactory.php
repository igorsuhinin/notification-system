<?php

declare(strict_types=1);

namespace App\Notification\Domain\Factory;

use App\Notification\Domain\Entity\NotificationEntity;
use App\Notification\Domain\Enum\ChannelTypeEnum;
use App\Notification\Domain\ValueObject\MessageBody;
use App\Notification\Domain\ValueObject\MessageSubject;
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
