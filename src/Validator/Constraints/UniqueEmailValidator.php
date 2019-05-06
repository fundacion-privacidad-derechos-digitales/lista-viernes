<?php

namespace App\Validator\Constraints;

use App\Entity\Email;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class UniqueEmailValidator extends ConstraintValidator
{
    private $entityManager;

    public function __construct(ObjectManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueEmail) {
            throw new UnexpectedTypeException($constraint, UniqueEmail::class);
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

        $foundEmails = $this->entityManager
            ->getRepository(Email::class)
            ->findByEmailHash($value);

        if (count($foundEmails) > 0) {
            $this->context->buildViolation($constraint->message)->addViolation();
            return;
        }
    }
}