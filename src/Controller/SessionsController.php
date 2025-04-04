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
        // je récupère les sessions et les trie dans l'ordre croissant par le début de la session puis par la fin de la session
        $sessions = $sessionRepository->findBy([], ['beginSession' =>"ASC",'finishSession' =>"ASC"]);
        return $this->render('session/index.html.twig', [
            'sessions' => $sessions,
        ]);
    }


    #[Route('/sessions/new', name:'new_session')]
    #[Route('/sessions/{id}/edit', name:'edit_session')]
    public function new (Session $session=null, Request $request, EntityManagerInterface $entityManager) : Response
    {
        // si la session n'est pas donné en praramètre alors je crée une nouvelle session
        if (!$session){
            $session = new Session();
        }
        // on crée un formulaire 
        $form = $this->createForm(SessionType::class, $session);
        // on dit que l'on veut traiter le formulaire 
        $form->handleRequest($request);
        // si le formulaire est envoyé et valide
        if ($form->isSubmitted() && $form->isValid())
        {
            // on récupère les données
            $session = $form->getData();
            // puis on fait un 'prepare' puis 'query' afin de mettre a jour la base de données
            $entityManager->persist($session);
            $entityManager->flush();

            return $this->redirectToRoute('app_sessions');
        }
        return $this->render('session/new.html.twig', ['formSession' => $form, 'edit' => $session->getId()]);
    }

    #[Route('/session/{id}/delete', name:'delete_session')]
    public function delete(Session $session, EntitymanagerInterface $entityManager) :Response
    {
        // je supprime la session et met aa jour la base de données
        $entityManager->remove($session);
        $entityManager->flush();

        return $this->redirectToRoute('app_sessions');
    }

    #[Route('/session/{idSession}/removeIntern/{idIntern}', name:'remove_intern')]
    public function remove_intern ($idSession, $idIntern,  SessionRepository $sessionRepository, InternRepository $internRepository, EntityManagerInterface $entityManager) :Response
    {
        // je recupere la session ou je veux retirer le stagiaire
        $session = $sessionRepository->findOneBy(['id'=> $idSession]);
        // je recupere l'intern que je veux supprimer dans la session
        $intern = $internRepository->findOneBy(['id'=>$idIntern]);
        // je supprime l'intern de la session et met a jour la base de donnée
        $intern = $session->removeIntern($intern);
        $entityManager->persist($intern);
        $entityManager->flush();
        return $this->redirectToRoute('detail_session', ['id'=> $session->getId()] );
    }

    #[Route('/session/{idSession}/newIntern/{idIntern}', name:'new_intern_session')]
    public function new_intern ($idSession, $idIntern, SessionRepository $sessionRepository, InternRepository $internRepository, EntityManagerInterface $entityManager) :Response
    {
        // je recupere session et intern
        $session = $sessionRepository->findOneBy(['id'=> $idSession]);
        $intern = $internRepository->findOneBy(['id' => $idIntern]);
        // je donne les varleurs a session
        $session->getId($session->getId());
        $session->addIntern($intern);
        // et je fais l'ajout dans la bdd
        $entityManager->persist($session);
        $entityManager->flush();
        
        return $this->redirectToRoute('detail_session', ['id' => $session->getId()]);
    }

    #[Route('/session/{idSession}/newProgram/{idCourse}', name:"new_program")]
    public function new_program ($idSession, $idCourse, SessionRepository $sessionRepository, Request $request, CourseRepository $courseRepository, ProgramRepository $programRepository, EntityManagerInterface $entityManager) : Response 
    {
        // je recupere la session et le module
        $session = $sessionRepository->findOneBy(['id'=> $idSession]);
        $course = $courseRepository->findOneBy(['id' => $idCourse]);
        // je cree un nouveau programme 
        $program = new Program();
        // je verifie que le bouton à été cliqué
        if (isset($_POST['submit']))
        {
            $nbDay = $request->request->get('nbDay'); // je récupère la valeur du nombre de jour donnée 
            // je met mes informations dans program
            $program->setSession($session);
            $program->setCourse($course);
            $program->setNbDay($nbDay);
            // je l'ajoute dans la base de données 
            $entityManager->persist($program);
            $entityManager->flush();
        }
        return $this->redirectToRoute('detail_session', ['id' => $session->getId()]);
    }

    #[Route('/session/{idSession}/delete/{idCourse}', name:'delete_courseS')]
    public function delete_courseS ($idSession, $idCourse, CourseRepository $courseRepository,  ProgramRepository $programRepository, SessionRepository $sessionRepository, EntityManagerInterface $entityManager) : Response 
    {
        // je récupère une session, un module et un program selon la session et le module
        $session = $sessionRepository->finOnedBy(['id'=>$idSession]); 
        $course = $courseRepository->findOneBy(['id'=>$idCourse]);
        $program = $programRepository->findOneBy(['session'=>$session->getId(), 'course' => $course->getId()]);
        // je supprime le programme et met a jour dans la base de données
        $program = $session->removeProgram($program);
        $entityManager->persist($program);
        $entityManager->flush();

        return $this->redirectToRoute('detail_session', ['id' =>$session->getId()]);
    }

    #[Route('/session/{id}', name:'detail_session')]
    public function detail (Session $session = null, SessionRepository $sessionRepository, InternRepository $internRepository) :Response
    {
        // je récupère les stagiaires/ modules qui ne sont pas inscrits et tous les stagiaires pour donner le nombre de staagiaire
        $nonInscrits = $sessionRepository->findNonInscrits( $session->getId() );
        $interns = $internRepository->findAll();
        $courseNotIn = $sessionRepository->NonCourse($session->getId());

        return $this->render('session/detail.html.twig', [
            'session' => $session,
            'nbInterns' => count($interns),
            'nonInscrits' => $nonInscrits,
            'courseNotIn' => $courseNotIn
        ]);
    }
}
