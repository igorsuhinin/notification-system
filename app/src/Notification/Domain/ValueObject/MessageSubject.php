<?php

declare(strict_types=1);

namespace App\Notification\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class MessageSubject
{
    /**
     * @var non-empty-string
     */
    #[ORM\Column(type: 'string')]
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
