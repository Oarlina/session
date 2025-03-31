<?php

namespace App\Controller;

use App\Entity\Intern;
use App\Entity\Session;
use App\Form\SessionType;
use App\Form\InternSessionType;
use App\Repository\InternRepository;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SessionsController extends AbstractController
{
    #[Route('/sessions', name: 'app_sessions')]
    public function index( SessionRepository $sessionRepository): Response
    {
        $sessions = $sessionRepository->findBy([], ['beginSession' =>"ASC",'finishSession' =>"ASC"]);
        return $this->render('session/index.html.twig', [
            'sessions' => $sessions,
        ]);
    }


    #[Route('/sessions/new', name:'new_session')]
    #[Route('/sessions/{id}/edit', name:'edit_session')]
    public function new (Session $session=null, Request $request, EntityManagerInterface $entityManager) : Response
    {
        if (!$session){
            $session = new Session();
        }

        $form = $this->createForm(SessionType::class, $session);
        $form->handleRequest($request);
        // dd($form);
        if ($form->isSubmitted() && $form->isValid())
        {
            $session = $form->getData();

            $entityManager->persist($session);
            $entityManager->flush();

            return $this->redirectToRoute('app_sessions');
        }
        return $this->render('session/new.html.twig', ['formSession' => $form, 'edit' => $session->getId()]);
    }

    #[Route('/session/{id}/delete', name:'delete_session')]
    public function delete(Session $session, EntitymanagerInterface $entityManager) :Response
    {
        $entityManager->remove($session);
        $entityManager->flush();

        return $this->redirectToRoute('app_sessions');
    }

    #[Route('/session/{idSession}/removeIntern/{idIntern}', name:'remove_intern')]
    public function remove_intern ($idSession, $idIntern,  SessionRepository $sessionRepository, InternRepository $internRepository, EntityManagerInterface $entityManager) :Response
    {
        $session = $sessionRepository->findBy(['id'=> $idSession]);
        $intern = $internRepository->findBy(['id'=>$idIntern]);
        $session = $session[0];
        $intern = $session->removeIntern($intern[0]);
        // dd($intern);
        $entityManager->persist($intern);
        $entityManager->flush();
        return $this->redirectToRoute('detail_session', ['id'=> $session->getId()] );
    }

    #[Route('/session/{idSession}/newIntern', name:'new_intern_session')]
    public function new_intern ($idSession, Request $request, SessionRepository $sessionRepository, EntityManagerInterface $entityManager) :Response
    {
        $session = $sessionRepository->findBy(['id'=> $idSession]);
        $session = $session[0]; // pour récuyperer la premiere et unique valeur du tableau donnée
        $interns = $sessionRepository->findNonInscrits( $session->getId() );
        // dd($interns);

        $form = $this->createForm(InternSessionType::class, $session);
        $form->handleRequest($request);
        // dd($form);
        if ($form->isSubmitted() && $form->isValid())
        {
            $session = $form->getData();
            $session->getId($session->getId());
            $entityManager->persist($session);
            $entityManager->flush();

            return $this->redirectToRoute('detail_session', ['id' => $session->getId()]);
        }

        return $this->render('session/newIntern.html.twig', ['formInternSession'=> $form, 'id'=> $session->getId(), 'interns' => $interns] );
    }



    #[Route('/session/{id}', name:'detail_session')]
    public function detail (Session $session = null, SessionRepository $sessionRepository, InternRepository $internRepository) :Response
    {
        $nonInscrits = $sessionRepository->findNonInscrits( $session->getId() );
        $interns = $internRepository->findAll();

        return $this->render('session/detail.html.twig', [
            'session' => $session,
            'nbInterns' => count($interns),
            'nonInscrits' => $nonInscrits
        ]);
    }
}
