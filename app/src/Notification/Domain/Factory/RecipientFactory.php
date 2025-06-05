<?php

declare(strict_types=1);

namespace App\Notification\Domain\Factory;

use App\Notification\Domain\Enum\ChannelTypeEnum;
use App\Notification\Domain\ValueObject\Recipient;

final class RecipientFactory
{
    /**
     * @param non-empty-string $to
     */
    public function create(ChannelTypeEnum $channelType, string $to): Recipient
    {
        return match ($channelType) {
            ChannelTypeEnum::EMAIL => new Recipient(email: $to),
            ChannelTypeEnum::SMS => new Recipient(phone: $to),
            ChannelTypeEnum::PUSH => new Recipient(pushToken: $to),
        };
    }
}
