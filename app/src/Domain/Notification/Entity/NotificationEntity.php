<?php

declare(strict_types=1);

namespace NotificationSystem\Domain\Notification\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use NotificationSystem\Domain\Notification\Enum\ChannelTypeEnum;
use NotificationSystem\Domain\Notification\Enum\NotificationStatusEnum;
use NotificationSystem\Domain\Notification\ValueObject\MessageBody;
use NotificationSystem\Domain\Notification\ValueObject\MessageSubject;
use NotificationSystem\Domain\Notification\ValueObject\Recipient;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'notifications')]
final class NotificationEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\Embedded(class: Recipient::class)]
    private Recipient $recipient;

    #[ORM\Column(enumType: ChannelTypeEnum::class)]
    private ChannelTypeEnum $channel;

    #[ORM\Embedded(class: MessageSubject::class)]
    private MessageSubject $subject;

    #[ORM\Embedded(class: MessageBody::class)]
    private MessageBody $body;

    #[ORM\Column(enumType: NotificationStatusEnum::class)]
    private NotificationStatusEnum $status;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $sentAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $deliveredAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $failedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $lastUpdatedAt = null;

    public function __construct(
        Uuid $id,
        Recipient $recipient,
        ChannelTypeEnum $channel,
        MessageSubject $subject,
        MessageBody $body,
    ) {
        $this->id = $id;
        $this->recipient = $recipient;
        $this->channel = $channel;
        $this->subject = $subject;
        $this->body = $body;
        $this->status = NotificationStatusEnum::QUEUED;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getRecipient(): Recipient
    {
        return $this->recipient;
    }

    public function getChannel(): ChannelTypeEnum
    {
        return $this->channel;
    }

    public function getSubject(): MessageSubject
    {
        return $this->subject;
    }

    public function getBody(): MessageBody
    {
        return $this->body;
    }

    public function getStatus(): NotificationStatusEnum
    {
        return $this->status;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getSentAt(): ?DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function getDeliveredAt(): ?DateTimeImmutable
    {
        return $this->deliveredAt;
    }

    public function getFailedAt(): ?DateTimeImmutable
    {
        return $this->failedAt;
    }

    public function getLastUpdatedAt(): ?DateTimeImmutable
    {
        return $this->lastUpdatedAt;
    }

    public function markAsSent(): void
    {
        $this->status = NotificationStatusEnum::SENT;
        $this->sentAt = $this->lastUpdatedAt = new DateTimeImmutable();
    }

    public function markAsDelivered(): void
    {
        $this->status = NotificationStatusEnum::DELIVERED;
        $this->deliveredAt = $this->lastUpdatedAt = new DateTimeImmutable();
    }

    public function markAsFailed(): void
    {
        $this->status = NotificationStatusEnum::FAILED;
        $this->failedAt = $this->lastUpdatedAt = new DateTimeImmutable();
    }
}
