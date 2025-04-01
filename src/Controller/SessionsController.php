<?php

namespace App\Controller;

use App\Entity\Intern;
use App\Entity\Program;
use App\Entity\Session;
use App\Form\ProgramType;
use App\Form\SessionType;
use App\Form\SessionInternType;
use App\Repository\CourseRepository;
use App\Repository\InternRepository;
use App\Repository\ProgramRepository;
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

        $form = $this->createForm(SessionInternType::class, $session);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid())
        {
            $session = $form->getData();
            $session->getId($session->getId());
            //si le nombre d'intenr sélectionner est supérieur au nombre de place alors on affiche une erreur
            if (count($session->getInterns()) > $session->getNbPlace()){
                $error = "Trop de stagiaire sélectionnés veuillé en selectionnés MAXIMUM ". $session->getNbPlace();
                return $this->render('session/newIntern.html.twig', ['formSessionIntern'=> $form, 'id'=> $session->getId(), 'interns' => $interns, 'error' =>$error] );
            }
            $entityManager->persist($session);
            $entityManager->flush();

            return $this->redirectToRoute('detail_session', ['id' => $session->getId()]);
        }

        return $this->render('session/newIntern.html.twig', ['formSessionIntern'=> $form, 'id'=> $session->getId(), 'interns' => $interns, 'error' => ''] );
    }

    #[Route('/session/{idSession}/newProgram', name:"new_program")]
    public function new_program ($idSession, SessionRepository $sessionRepository, Request $request, CourseRepository $courseRepository, ProgramRepository $programRepository, EntityManagerInterface $entityManager) : Response 
    {
        $session = $sessionRepository->findBy(['id'=> $idSession]);
        $session = $session[0]; // pour récuyperer la premiere et unique valeur du tableau donnée
        $course = $courseRepository->findAll();
        $program = new Program();
        
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request); 
        if ($form->isSubmitted() && $form->isValid()) {
            $program = $form->getData();
            $program->setSession($session);
            
            // on cherche si le module est déjà dans la session
            $programTest = $programRepository->findBy(['course' => $program->getCourse(), 'session' => $session->getId()]);
            // si on en trouve on retourne au formulaire et signale que le module existe déjà dedans
            
            if ($programTest != []){ // si programTest n'est pas un tableau vide alors il existe deja
                $error = "Le module existe déjà";
                return $this->render('session/newProgram.html.twig', ['formProgram'=> $form, 'id'=> $session->getId(), 'error' => $error] );
            }

            $entityManager->persist($program);
            $entityManager->flush();

            return $this->redirectToRoute('detail_session', ['id' => $session->getId()]);

        }
        return $this->render('session/newProgram.html.twig', ['formProgram'=> $form, 'id'=> $session->getId(), 'error' => ''] );
    }

    #[Route('/session/{idSession}/delete/{idCourse}', name:'delete_course')]
    public function delete_course ($idSession, $idCourse, CourseRepository $courseRepository,  ProgramRepository $programRepository, SessionRepository $sessionRepository, EntityManagerInterface $entityManager) : Response {
        // $program = $programRepository->findBy(['id' => $idProgram]);
        $session = $sessionRepository->findBy(['id'=>$idSession])[0]; 
        $course = $courseRepository->findBy(['id'=>$idCourse])[0];
        $program = $programRepository->findBy(['session'=>$session->getId(), 'course' => $course->getId()])[0];
        // dd($program);
        $program = $session->removeProgram($program);

        $entityManager->persist($program);
        $entityManager->flush();

        return $this->redirectToRoute('detail_session', ['id' =>$session->getId()]);
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
