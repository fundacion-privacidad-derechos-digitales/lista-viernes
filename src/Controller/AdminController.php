<?php

namespace App\Controller;

use App\Entity\Email;
use App\Entity\PartyUser;
use App\Entity\Phone;
use App\Entity\User;
use App\Form\EditPartyUserFormType;
use App\Form\PartyUserFormType;
use App\Service\EmailManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{
    /**
     * @Route("/lv-puerta", name="admin_dashboard")
     */
    public function dashboard(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        return $this->render('admin/index.html.twig', [
            'user' => $this->getUser(),
            'numberUsers' => $entityManager->getRepository(User::class)->countAll(),
            'numberUsersValidated' => $entityManager->getRepository(User::class)->countValidated(),
            'numberEmails' => $entityManager->getRepository(Email::class)->countAll(),
            'numberEmailsValidated' => $entityManager->getRepository(Email::class)->countValidated(),
            'numberPhones' => $entityManager->getRepository(Phone::class)->countAll(),
        ]);
    }

    /**
     * @Route("/lv-puerta/partidos", name="admin_party_show")
     */
    public function partyShow(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        return $this->render('admin/partyShow.html.twig', [
            'user' => $this->getUser(),
            'parties' => $entityManager->getRepository(PartyUser::class)->findAll(),
        ]);
    }

    /**
     * @Route("/lv-puerta/partidos/nuevo", name="admin_party_new")
     */
    public function partyNew(Request $request, UserPasswordEncoderInterface $passwordEncoder, EmailManager $emailManager): Response
    {
        $partyUser = new PartyUser();

        $form = $this->createForm(PartyUserFormType::class, $partyUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            // generate and encode the password
            $pass = $partyUser->generateRandomPassword();
            $partyUser->setPassword($passwordEncoder->encodePassword($partyUser, $pass));
            $partyUser->generateEmailValidationToken();

            $entityManager->persist($partyUser);
            $entityManager->flush();

            $emailManager->sendPartyActivateAccountEmail($partyUser);

            $this->addFlash('notice', 'flash.partyUserCreatedSuccessfully');

            return $this->redirectToRoute('admin_party_show');
        }

        return $this->render('admin/partyNew.html.twig', [
            'partyForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/lv-puerta/partidos/{id}/editar", name="admin_party_edit")
     */
    public function partyEdit(Request $request, PartyUser $partyUser): Response
    {
        $form = $this->createForm(EditPartyUserFormType::class, $partyUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($partyUser);
            $entityManager->flush();

            $this->addFlash('notice', 'flash.partyUserSavedSuccessfully');

            return $this->redirectToRoute('admin_party_show');
        }

        return $this->render('admin/partyEdit.html.twig', [
            'partyForm' => $form->createView(),
        ]);
    }
}
