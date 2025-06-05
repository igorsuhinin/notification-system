<?php

declare(strict_types=1);

namespace App\Notification\Application\Contract;

use App\Notification\Domain\Enum\ChannelTypeEnum;
use App\Notification\Domain\ValueObject\Recipient;

interface RecipientToProviderInterface
{
    /**
     * @return non-empty-string
     */
    public function get(Recipient $recipient, ChannelTypeEnum $channelType): string;
}
