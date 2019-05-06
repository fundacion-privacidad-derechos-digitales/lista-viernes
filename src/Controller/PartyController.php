<?php

namespace App\Controller;

use App\Entity\CompareTemp;
use App\Entity\MatchJob;
use App\Form\MatchJobFormType;
use App\Form\PartyChangePasswordFormType;
use App\Service\EmailManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PartyController extends AbstractController
{
    /**
     * @Route("/partido", name="party_index")
     */
    public function dashboard(): Response
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('party/index.html.twig', [
            'matchJobs' => $em->getRepository(MatchJob::class)->findBy([
                'partyUser' => $this->getUser()->getId()
            ]),
        ]);
    }

    /**
     * @Route("/partido/cambiar-password", name="party_change_password"): Response
     */
    public function partyChangePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder, EmailManager $emailManager)
    {
        $user = $this->getUser();

        $form = $this->createForm(PartyChangePasswordFormType::class, $user);
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

            $emailManager->sendPartyPasswordChangedEmail($user);

            $this->addFlash('notice', 'flash.passwordUpdatedSuccessfully');

            return $this->redirectToRoute('party_index');
        }

        return $this->render('party/changePassword.html.twig', [
            'changePasswordForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/partido/match/new", name="party_matchjob_new")
     */
    public function matchJobNew(Request $request, Filesystem $filesystem)
    {
        $matchJob = new MatchJob();
        $form = $this->createForm(MatchJobFormType::class, $matchJob);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filesystem->dumpFile(
                $this->getParameter('matches_directory') . '/' . $matchJob->getFilename(),
                $form->get('fileData')->getData()
            );

            $matchJob->setPartyUser($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($matchJob);
            $entityManager->flush();

            return $this->redirect($this->generateUrl('party_index'));
        }

        return $this->render('party/matchJobNew.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
