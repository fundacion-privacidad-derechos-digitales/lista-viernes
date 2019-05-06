<?php

namespace App\Validator\Constraints;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class MaxEmailsValidator extends ConstraintValidator
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof MaxEmails) {
            throw new UnexpectedTypeException($constraint, MaxEmails::class);
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

        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user instanceof User) {
            if ($user->getEmails()->count() >= $constraint->max) {
                $this->context->buildViolation($constraint->message)->addViolation();
                return;
            }
        }
    }
}