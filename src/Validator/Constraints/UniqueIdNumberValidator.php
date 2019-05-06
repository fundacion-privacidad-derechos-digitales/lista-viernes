<?php

namespace App\Validator\Constraints;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class UniqueIdNumberValidator extends ConstraintValidator
{
    private $entityManager;
    private $tokenStorage;

    public function __construct(ObjectManager $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueIdNumber) {
            throw new UnexpectedTypeException($constraint, UniqueIdNumber::class);
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

        if ($user instanceof User && $value === $user->getIdNumber()) {
            return;
        }

        $foundUsers = $this->entityManager
            ->getRepository(User::class)
            ->findByIdNumberHashValidated($value);

        if (count($foundUsers) > 0) {
            $this->context->buildViolation($constraint->message)->addViolation();
            return;
        }
    }
}