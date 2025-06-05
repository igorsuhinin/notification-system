<?php

declare(strict_types=1);

namespace App\Notification\Application\Provider;

use App\Notification\Application\Contract\RecipientToProviderInterface;
use App\Notification\Domain\Enum\ChannelTypeEnum;
use App\Notification\Domain\ValueObject\Recipient;
use LogicException;
use Override;

final class RecipientToProvider implements RecipientToProviderInterface
{
    #[Override]
    public function get(Recipient $recipient, ChannelTypeEnum $channelType): string
    {
        $to = match ($channelType) {
            ChannelTypeEnum::EMAIL => $recipient->getEmail(),
            ChannelTypeEnum::SMS => $recipient->getPhone(),
            ChannelTypeEnum::PUSH => $recipient->getPushToken(),
        };

        if (null === $to) {
            throw new LogicException("Recipient To value for channel \"{$channelType->value}\" not set");
        }

        return $to;
    }
}
