<?php

namespace App\Controller;

use App\Encrypt\EncryptionService;
use App\Entity\Email;
use App\Entity\User;
use App\Hash\HashService;
use App\Service\EmailManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ValidateEmailController extends AbstractController
{
    /**
     * @Route("/activar-cuenta", name="app_activate_account")
     */
    public function activateAccount(Request $request, EmailManager $emailManager, EncryptionService $encryptionService, HashService $hashService)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneByEmailValidationToken($request->query->get('token'));

        if ($user === null) {
            $this->addFlash('error', 'flash.emailValidationFailed');
        } else {
            $userAccountsAlreadyValidated = $this->getDoctrine()
                ->getRepository(User::class)
                ->findByIdNumberHashValidated($user->getIdNumber());

            if (count($userAccountsAlreadyValidated) > 0) {
                $this->addFlash('error', 'flash.emailValidationFailedIdNumber');
            } elseif ($user->getEmailValidated() === true) {
                $this->addFlash('notice', 'flash.emailAlreadyValidated');
            } else {
                $user->setEmailValidated(true);
                $email = new Email();
                $email->setEmail($user->getEmail());
                $email->setValidated(true);
                $email->setValidationToken($user->getEmailValidationToken());
                $user->addEmail($email);

                $encryptionService->encrypt($email);
                $hashService->hash($email);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->getRepository(Email::class)->deleteAllByHash($email->getEmail());
                $entityManager->persist($user);
                $entityManager->persist($email);
                $entityManager->flush();

                $emailManager->sendWelcomeEmail($user);

                $this->addFlash('notice', 'flash.accountActivated');
            }
        }

        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/validar-email", name="app_validate_email")
     */
    public function validateEmail(Request $request)
    {
        $email = $this->getDoctrine()
            ->getRepository(Email::class)
            ->findOneByValidationToken($request->query->get('token'));

        if ($email === null) {
            $this->addFlash('error', 'flash.emailValidationFailed');
        } else {
            $email->setValidated(true);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($email);
            $entityManager->flush();

            $this->addFlash('notice', 'flash.emailValidatedSuccessfully');
        }

        return $this->redirectToRoute('app_profile');
    }
}