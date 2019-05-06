<?php

namespace App\Controller;

use App\Entity\AdminUser;
use App\Entity\PartyUser;
use App\Entity\User;
use App\Form\PartyResetPasswordFormType;
use App\Form\ResetPasswordFormType;
use App\Service\EmailManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class PartySecurityController extends AbstractController
{
    /**
     * @Route("/partido/login", name="party_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security_party/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/partido/logout", name="party_logout")
     */
    public function logout()
    {
        // controller can be blank: it will never be executed!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    /**
     * @Route("/partido/reestablecer-password", name="party_reset_password")
     */
    public function resetPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder, EmailManager $emailManager)
    {
        $form = $this->createForm(PartyResetPasswordFormType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $formData = $form->getData();

            $user = $this->getDoctrine()
                ->getRepository(PartyUser::class)
                ->findOneBy(['email' => $formData['email']]);

            if ($user === null) {
                $this->addFlash('error', 'flash.userDoesNotExist');

                return $this->redirectToRoute('party_reset_password');
            }

            $newPassword = $user->generateRandomPassword();

            $user->setPassword($passwordEncoder->encodePassword($user, $newPassword));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $emailManager->sendPartyResetPasswordEmail($user, $newPassword);

            $this->addFlash('notice', 'flash.resetPasswordEmailSent');

            return $this->redirectToRoute('party_login');
        }

        // TODO hacer el twig reset password
        return $this->render('security_party/resetPassword.html.twig', [
            'resetPasswordForm' => $form->createView(),
        ]);
    }
}
