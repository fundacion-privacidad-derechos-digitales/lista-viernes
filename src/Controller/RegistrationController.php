<?php

namespace App\Controller;

use App\Encrypt\EncryptionService;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Hash\HashService;
use App\Service\EmailManager;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/registro", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, EmailManager $emailManager, EncryptionService $encryptionService, HashService $hashService): Response
    {

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password
            $user->setPassword($passwordEncoder->encodePassword($user, $form->get('plainPassword')->getData()));

            $user->generateEmailValidationToken();

            $encryptionService->encrypt($user);
            $hashService->hash($user);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $emailManager->sendActivateAccountEmail($user);

            $this->addFlash('notice', 'flash.verificationEmailSent');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
