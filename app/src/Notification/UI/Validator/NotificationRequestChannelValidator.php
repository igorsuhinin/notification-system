<?php

declare(strict_types=1);

namespace App\Notification\UI\Validator;

use App\Notification\Domain\Enum\ChannelTypeEnum;
use App\Notification\UI\Request\NotificationRequest;
use App\Notification\UI\Validator\Constraint\NotificationRequestChannelConstraint;
use Override;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @psalm-suppress PropertyNotSetInConstructor Suppress error "$context is not defined in constructor"
 */
final class NotificationRequestChannelValidator extends ConstraintValidator
{
    public function __construct(private readonly ValidatorInterface $validator)
    {
    }

    #[Override]
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof NotificationRequestChannelConstraint) {
            throw new UnexpectedTypeException($constraint, NotificationRequestChannelConstraint::class);
        }

        if (!$value instanceof NotificationRequest) {
            throw new UnexpectedValueException($value, NotificationRequest::class);
        }

        $violations = match ($value->channel) {
            ChannelTypeEnum::EMAIL->value => $this->validator->validate(
                $value->to,
                [new Assert\Email(message: 'notification.to.invalid_email')]
            ),
            ChannelTypeEnum::SMS->value => $this->validator->validate(
                $value->to,
                [new Assert\Regex(pattern: '/^\+?[0-9]{10,15}$/', message: 'notification.to.invalid_phone')]
            ),
            ChannelTypeEnum::PUSH->value => $this->validator->validate(
                $value->to,
                [new Assert\Length(
                    min: 5,
                    max: 100,
                    minMessage: 'notification.to.push_token_too_short',
                    maxMessage: 'notification.to.push_token_too_long'
                )]
            ),
            default => null,
        };

        if ($violations && count($violations) > 0) {
            foreach ($violations as $violation) {
                $this->context->buildViolation((string) $violation->getMessage())
                    ->atPath('to')
                    ->addViolation();
            }
        }
    }
}
