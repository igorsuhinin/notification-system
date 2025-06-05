<?php

declare(strict_types=1);

namespace App\Notification\UI\Validator\Constraint;

use App\Notification\UI\Validator\NotificationRequestChannelValidator;
use Attribute;
use Override;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_CLASS)]
final class NotificationRequestChannelConstraint extends Constraint
{
    #[Override]
    public function validatedBy(): string
    {
        return NotificationRequestChannelValidator::class;
    }

    #[Override]
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
