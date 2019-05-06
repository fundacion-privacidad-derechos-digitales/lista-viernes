<?php

namespace App\Controller;

use App\Encrypt\EncryptionService;
use App\Entity\Email;
use App\Entity\Phone;
use App\Entity\User;
use App\Form\ChangeEmailFormType;
use App\Form\ChangePasswordFormType;
use App\Form\DeleteAccountFormType;
use App\Form\EmailFormType;
use App\Form\PhoneFormType;
use App\Form\ProfileFormType;
use App\Hash\HashService;
use App\Service\EmailManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route("/perfil", name="app_profile")
     */
    public function profile(): Response
    {
        return $this->render('profile/index.html.twig', ['user' => $this->getUser()]);
    }

    /**
     * @Route("/perfil/editar", name="app_profile_edit")
     */
    public function profileEdit(Request $request, EncryptionService $encryptionService, HashService $hashService): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encryptionService->encrypt($user);
            $hashService->hash($user);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('notice', 'flash.profileUpdatedSuccessfully');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/edit.html.twig', [
            'profileForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/perfil/cambiar-email", name="app_profile_change_email")
     */
    public function profileChangeEmail(Request $request, EmailManager $emailManager, EncryptionService $encryptionService, HashService $hashService): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ChangeEmailFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Email $managedEmail */
            $managedEmail = $user->getManagedEmail($user->getEmail());

            if (null === $managedEmail) {
                $user->generateEmailValidationToken();
                $user->setEmailValidated(false);
                $emailManager->sendActivateAccountEmail($user);
                $this->addFlash('notice', 'flash.emailUpdatedSuccessfullyValidateIt');
            } elseif ($managedEmail->getValidated() === false) {
                $user->setEmailValidationToken($managedEmail->getValidationToken());
                $emailManager->sendActivateAccountEmail($user);
                $this->addFlash('notice', 'flash.emailUpdatedSuccessfullyValidateIt');
            } else {
                $user->setEmailValidationToken($managedEmail->getValidationToken());
                $user->setEmailValidated(true);
                $this->addFlash('notice', 'flash.emailUpdatedSuccessfully');
            }

            $encryptionService->encrypt($user);
            $hashService->hash($user);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/changeEmail.html.twig', [
            'changeEmailForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/perfil/cambiar-password", name="app_profile_change_password"): Response
     */
    public function profileChangePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder, EmailManager $emailManager)
    {
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('newPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $emailManager->sendPasswordChangedEmail($user);

            $this->addFlash('notice', 'flash.passwordUpdatedSuccessfully');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/changePassword.html.twig', [
            'changePasswordForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/perfil/emails", name="app_profile_emails")
     */
    public function profileManageEmails(Request $request, EmailManager $emailManager, EncryptionService $encryptionService, HashService $hashService)
    {
        $user = $this->getUser();
        $email = new Email();

        $form = $this->createForm(EmailFormType::class, $email);
        $form->handleRequest($request);

        $formDelete = $this->createDeleteEmailForm();

        if ($form->isSubmitted() && $form->isValid()) {

            $user->addEmail($email);
            $email->generateValidationToken();

            $encryptionService->encrypt($email);
            $hashService->hash($email);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($email);
            $entityManager->flush();

            $emailManager->sendValidateEmailEmail($user, $email);

            $this->addFlash('notice', 'flash.verificationEmailSent');

            return $this->redirectToRoute('app_profile_emails');
        }

        return $this->render('profile/manageEmails.html.twig', [
            'emails' => $user->getEmails(),
            'emailForm' => $form->createView(),
            'deleteForm' => $formDelete->createView()
        ]);
    }

    /**
     * @Route("/perfil/email/{id}/delete", name="app_profile_email_delete")
     */
    public function profileManageEmailDelete(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $email = $em->getRepository(Email::class)->find($id);


        if ($email === null){
            throw $this->createNotFoundException();
        }

        $user = $this->getUser();
        if($user->getId() != $email->getUser()->getId()){
            throw $this->createNotFoundException();
        }

        $form = $this->createDeleteEmailForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $em->remove($email);
            $em->flush();

            $this->addFlash('notice', 'flash.deleteEmail');
        }

        return $this->redirectToRoute('app_profile_emails');
    }

    /**
     * @Route("/perfil/telefonos", name="app_profile_phones")
     */
    public function profileManagePhones(Request $request, EncryptionService $encryptionService, HashService $hashService)
    {
        $user = $this->getUser();
        $phone = new Phone();

        $formDelete = $this->createDeletePhoneForm();

        $form = $this->createForm(PhoneFormType::class, $phone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->addPhone($phone);

            $encryptionService->encrypt($phone);
            $hashService->hash($phone);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($phone);
            $entityManager->flush();

            $this->addFlash('notice', 'flash.phoneAddedSuccessfully');

            return $this->redirectToRoute('app_profile_phones');
        }

        return $this->render('profile/managePhones.html.twig', [
            'phones' => $user->getPhones(),
            'phoneForm' => $form->createView(),
            'deleteForm' => $formDelete->createView()
        ]);
    }

    /**
     * @Route("/perfil/telefono/{id}/delete", name="app_profile_phone_delete")
     */
    public function profileManagePhoneDelete(Request $request, Phone $phone)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        if($user->getId() != $phone->getUser()->getId()){
            throw $this->createNotFoundException();
        }

        $form = $this->createDeletePhoneForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->remove($phone);
            $em->flush();

            $this->addFlash('notice', 'flash.deletePhone');
        }

        return $this->redirectToRoute('app_profile_phones');
    }

    /**
     * @Route("/perfil/borrar-cuenta", name="app_profile_delete_account")
     */
    public function profileDeleteAccount(Request $request, EmailManager $emailManager)
    {
        $form = $this->createForm(DeleteAccountFormType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $deleteAccountEmail = $user->getEmail();
            $this->get('security.token_storage')->setToken(null);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->getRepository(Email::class)->deleteAllByUser($user);
            $entityManager->getRepository(Phone::class)->deleteAllByUser($user);
            $entityManager->remove($user);
            $entityManager->flush();

            $emailManager->sendDeleteAccountSuccessfulEmail($deleteAccountEmail);
            $this->addFlash('notice', 'flash.accountDeletedSuccessfully');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('profile/deleteAccount.html.twig', [
            'deleteAccountForm' => $form->createView()
        ]);
    }

    private function createDeleteEmailForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_profile_email_delete', ['id' => '__replace__']))
            ->setMethod('DELETE')
            ->getForm();
    }

    private function createDeletePhoneForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_profile_phone_delete', ['id' => '__replace__']))
            ->setMethod('DELETE')
            ->getForm();
    }
}
