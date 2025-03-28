<?php

namespace App\Controller;

use App\Entity\Intern;
use App\Entity\Session;
use App\Form\SessionType;
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

    #[Route('/session/remove/intern/{id}/{idSession}', name:'remove_intern')]
    public function remove_intern (Intern $intern, Session $session, SessionRepository $sessionRepository) :Response
    {
        $intern = $sessionRepository->removeIntern($intern->getId());
        dd($intern);
        return $this->redirectToRoute('detail_session');
    }
    
    #[Route('/session/{id}', name:'detail_session')]
    public function detail (Session $session) :Response
    {
        return $this->render('session/detail.html.twig', [
            'session' => $session
        ]);
    }
}
