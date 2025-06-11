<?php

declare(strict_types=1);

namespace NotificationSystem\UI\Notification\Validator\Constraint;

use Attribute;
use NotificationSystem\UI\Notification\Validator\NotificationRequestChannelValidator;
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
