<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class PasswordStrengthValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof PasswordStrength) {
            throw new UnexpectedTypeException($constraint, PasswordStrength::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, 'string');
        }

        if (!preg_match('/[A-Z]/', $value)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }

        if (!preg_match('/[a-z]/', $value)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }

        if (!preg_match('/[0-9]/', $value)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}