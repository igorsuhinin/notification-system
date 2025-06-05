<?php

declare(strict_types=1);

namespace App\Notification\UI\Request;

use App\Notification\UI\Validator\Constraint\NotificationRequestChannelConstraint;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    description: 'Notification request payload',
    required: ['channel', 'content', 'subject', 'to'],
)]
#[NotificationRequestChannelConstraint]
final readonly class NotificationRequest
{
    /**
     * @param non-empty-string $channel
     * @param non-empty-string $content
     * @param non-empty-string $subject
     * @param non-empty-string $to
     */
    public function __construct(
        #[Assert\NotBlank(message: 'notification.channel.not_blank')]
        #[Assert\Choice(options: ['email', 'sms', 'push'], message: 'notification.channel.invalid_choice')]
        #[OA\Property(description: 'Notification channel', type: 'string', enum: ['email', 'sms', 'push'])]
        public string $channel,
        #[Assert\NotBlank(message: 'notification.content.not_blank')]
        #[OA\Property(description: 'Notification content', type: 'string')]
        public string $content,
        #[Assert\NotBlank(message: 'notification.subject.not_blank')]
        #[OA\Property(description: 'Notification subject', type: 'string')]
        public string $subject,
        #[Assert\NotBlank(message: 'notification.to.not_blank')]
        #[OA\Property(description: 'Recipient identifier (email, phone, or push token)', type: 'string')]
        public string $to, // email OR phone OR pushToken
    ) {
    }
}
