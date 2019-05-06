<?php

namespace App\Controller;

use App\Entity\PartyUser;
use App\Service\EmailManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PartyValidateEmailController extends AbstractController
{
    /**
     * @Route("/partido/activar-cuenta", name="party_activate_account")
     */
    public function activateAccount(Request $request, UserPasswordEncoderInterface $passwordEncoder, EmailManager $emailManager)
    {
        $partyUser = $this->getDoctrine()
            ->getRepository(PartyUser::class)
            ->findOneByEmailValidationToken($request->query->get('token'));

        if ($partyUser === null) {
            $this->addFlash('error', 'flash.emailValidationFailed');
        } else {
            $partyUser->setEmailValidated(true);
            $pass = $partyUser->generateRandomPassword();
            $partyUser->setPassword($passwordEncoder->encodePassword($partyUser, $pass));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($partyUser);
            $entityManager->flush();

            $emailManager->sendPartyWelcomeEmail($partyUser, $pass);

            $this->addFlash('notice', 'flash.accountActivated');
        }

        return $this->redirectToRoute('party_login');
    }
}