<?php

namespace App\Service;

use App\Entity\AdminUser;
use App\Entity\Email;
use App\Entity\PartyUser;
use \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Entity\User;

class EmailManager
{
    private $mailer;
    private $templating;
    private $emailFrom;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $templating, ParameterBagInterface $params)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->emailFrom = $params->get('email_from');
    }

    public function sendActivateAccountEmail(User $user)
    {
        $message = (new \Swift_Message('Verifica tu email en la Lista Viernes'))
            ->setFrom($this->emailFrom, 'Lista Viernes')
            ->setTo($user->getEmail())
            ->setBody($this->templating->render('emails/activateAccount.html.twig', ['user' => $user]), 'text/html');

        $this->mailer->send($message);
    }

    public function sendPartyActivateAccountEmail(PartyUser $partyUser)
    {
        $message = (new \Swift_Message('Verifica tu email en la Lista Viernes'))
            ->setFrom($this->emailFrom, 'Lista Viernes')
            ->setTo($partyUser->getEmail())
            ->setBody($this->templating->render('emails/partyActivateAccount.html.twig', ['user' => $partyUser]), 'text/html');

        $this->mailer->send($message);
    }

    public function sendWelcomeEmail(User $user)
    {
        $message = (new \Swift_Message('¡Feliz Privacidad y Bienvenid@!'))
            ->setFrom($this->emailFrom, 'Lista Viernes')
            ->setTo($user->getEmail())
            ->setBody($this->templating->render('emails/welcome.html.twig'), 'text/html');

        $this->mailer->send($message);
    }

    public function sendWelcomeAdminEmail(AdminUser $adminUser, $password)
    {
        $message = (new \Swift_Message('Lista Viernes - Usuario administrador'))
            ->setFrom($this->emailFrom, 'Lista Viernes')
            ->setTo($adminUser->getEmail())
            ->setBody($this->templating->render('emails/welcomeAdmin.html.twig', [
                'password' => $password
            ]), 'text/html');

        $this->mailer->send($message);
    }

    public function sendPartyWelcomeEmail(PartyUser $partyUser, $password)
    {
        $message = (new \Swift_Message('Lista Viernes - Usuario partido político'))
            ->setFrom($this->emailFrom, 'Lista Viernes')
            ->setTo($partyUser->getEmail())
            ->setBody($this->templating->render('emails/partyWelcome.html.twig', [
                'password' => $password
            ]), 'text/html');

        $this->mailer->send($message);
    }


    public function sendPasswordChangedEmail(User $user)
    {
        $message = (new \Swift_Message('¡Contraseña cambiada!'))
            ->setFrom($this->emailFrom, 'Lista Viernes')
            ->setTo($user->getEmail())
            ->setBody($this->templating->render('emails/passwordChanged.html.twig', ['user' => $user]), 'text/html');

        $this->mailer->send($message);
    }

    public function sendPartyPasswordChangedEmail(PartyUser $partyUser)
    {
        $message = (new \Swift_Message('¡Contraseña cambiada!'))
            ->setFrom($this->emailFrom, 'Lista Viernes')
            ->setTo($partyUser->getEmail())
            ->setBody($this->templating->render('emails/partyPasswordChanged.html.twig', ['user' => $partyUser]), 'text/html');

        $this->mailer->send($message);
    }

    public function sendResetPasswordEmail(User $user, string $newPassword)
    {
        $message = (new \Swift_Message('Recupera tu contraseña en la Lista Viernes'))
            ->setFrom($this->emailFrom, 'Lista Viernes')
            ->setTo($user->getEmail())
            ->setBody($this->templating->render('emails/resetPassword.html.twig', ['newPassword' => $newPassword]), 'text/html');

        $this->mailer->send($message);
    }

    public function sendPartyResetPasswordEmail(PartyUser $partyUser, string $newPassword)
    {
        $message = (new \Swift_Message('Recupera tu contraseña en la Lista Viernes'))
            ->setFrom($this->emailFrom, 'Lista Viernes')
            ->setTo($partyUser->getEmail())
            ->setBody($this->templating->render('emails/resetPassword.html.twig', ['newPassword' => $newPassword]), 'text/html');

        $this->mailer->send($message);
    }

    public function sendValidateEmailEmail(User $user, Email $email)
    {
        $body = $this->templating->render('emails/validateEmail.html.twig', ['user' => $user, 'email' => $email]);

        $message = (new \Swift_Message('Confirma tu nuevo email en la Lista Viernes'))
            ->setFrom($this->emailFrom, 'Lista Viernes')
            ->setTo($email->getEmail())
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }

    public function sendDeleteAccountSuccessfulEmail(string $deleteAccountEmail)
    {
        $body = $this->templating->render('emails/deleteAccountSuccessful.twig');

        $message = (new \Swift_Message('Cuenta eliminada de la Lista Viernes'))
            ->setFrom($this->emailFrom, 'Lista Viernes')
            ->setTo($deleteAccountEmail)
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }

    public function sendMatchJobFinishedEmail(string $partyAdminEmail)
    {
        $body = $this->templating->render('emails/matchJobFinished.html.twig');

        $message = (new \Swift_Message('La tarea de comparación ya esta lista'))
            ->setFrom($this->emailFrom, 'Lista Viernes')
            ->setTo($partyAdminEmail)
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }
}
