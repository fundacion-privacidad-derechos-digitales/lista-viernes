<?php

namespace App\Validator\Constraints;

use App\Entity\Email;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class UniqueAccountEmailValidator extends ConstraintValidator
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
        if (!$constraint instanceof UniqueAccountEmail) {
            throw new UnexpectedTypeException($constraint, UniqueAccountEmail::class);
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

        // If the user is logged in
        if ($user instanceof User) {
            // Check if is the same account email
            if ($value === $user->getEmail()) {
                return;
            }

            // Check if the email belongs to user emails
            foreach ($user->getEmails() as $email) {
                if ($value === $email->getEmail()) {
                    return;
                }
            }
        }

        $foundUsers = $this->entityManager
            ->getRepository(User::class)
            ->findByEmailHashValidated($value);

        if (count($foundUsers) > 0) {
            $this->context->buildViolation($constraint->message)->addViolation();
            return;
        }

        $foundEmails = $this->entityManager
            ->getRepository(Email::class)
            ->findByEmailHashValidated($value);

        if (count($foundEmails) > 0) {
            $this->context->buildViolation($constraint->message)->addViolation();
            return;
        }
    }
}