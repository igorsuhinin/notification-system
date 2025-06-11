<?php

declare(strict_types=1);

namespace NotificationSystem\Domain\Notification\ValueObject;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class Recipient
{
    /**
     * @var non-empty-string|null $email
     */
    #[ORM\Column(nullable: true)]
    private ?string $email;

    /**
     * @var non-empty-string|null $phone
     */
    #[ORM\Column(nullable: true)]
    private ?string $phone;

    /**
     * @var non-empty-string|null $pushToken
     */
    #[ORM\Column(name: 'push_token', nullable: true)]
    private ?string $pushToken;

    /**
     * @param non-empty-string|null $email
     * @param non-empty-string|null $phone
     * @param non-empty-string|null $pushToken
     */
    public function __construct(?string $email = null, ?string $phone = null, ?string $pushToken = null)
    {
        $this->email = $email;
        $this->phone = $phone;
        $this->pushToken = $pushToken;
    }

    /**
     * @return non-empty-string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return non-empty-string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @return non-empty-string|null
     */
    public function getPushToken(): ?string
    {
        return $this->pushToken;
    }
}
