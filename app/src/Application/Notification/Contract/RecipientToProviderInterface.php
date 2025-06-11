<?php

declare(strict_types=1);

namespace NotificationSystem\Application\Notification\Contract;

use NotificationSystem\Domain\Notification\Enum\ChannelTypeEnum;
use NotificationSystem\Domain\Notification\ValueObject\Recipient;

interface RecipientToProviderInterface
{
    /**
     * @return non-empty-string
     */
    public function get(Recipient $recipient, ChannelTypeEnum $channelType): string;
}
