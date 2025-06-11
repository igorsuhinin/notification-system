<?php

declare(strict_types=1);

namespace NotificationSystem\Application\Notification\Provider;

use LogicException;
use NotificationSystem\Application\Notification\Contract\RecipientToProviderInterface;
use NotificationSystem\Domain\Notification\Enum\ChannelTypeEnum;
use NotificationSystem\Domain\Notification\ValueObject\Recipient;
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
