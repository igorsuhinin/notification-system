<?php

declare(strict_types=1);

namespace NotificationSystem\Domain\Notification\ValueObject;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class MessageBody
{
    /**
     * @var non-empty-string
     */
    #[ORM\Column(type: 'text')]
    private string $value;

    /**
     * @param non-empty-string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return non-empty-string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
