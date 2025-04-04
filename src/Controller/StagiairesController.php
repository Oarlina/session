<?php

namespace App\Controller;

use App\Entity\Intern;
use App\Form\InternType;
use App\Repository\InternRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class StagiairesController extends AbstractController
{
    #[Route('/stagiaires', name: 'app_interns')]
    public function index(InternRepository $internRepository): Response
    {
        // je récupère tous les stagiaires et les trie par ordre craoissant via leur prénom
        $interns = $internRepository->findBy([],['name'=> 'ASC']);
        return $this->render('intern/index.html.twig', [
            'interns' => $interns,
        ]);
    }

    #[Route('interns/{id}/edit', name:'edit_intern')]
    #[Route('interns/new', name:'new_intern')]
    public function new(Intern $intern = null, Request $request, EntityManagerInterface $entityManager) : Response
    {
        // si intern n'est pas donnée en paramètre alors je crée un nouvel intern
        if (!$intern){
            $intern = new Intern();
        }
        // je cree le formulaire puis le récupère
        $form = $this->createForm(InternType::class, $intern);
        // on dit que l'on veut traiter le formulaire 
        $form->handleRequest($request);
        // je verifie que le formulaire est juste 
        if ($form->isSubmitted() && $form->isValid()){
            // on récupère les données
            $intern = $form->getData();
            // puis on fait un 'prepare' puis 'query' afin de mettre a jour la base de données
            $entityManager->persist($intern); 
            $entityManager->flush();

            return $this->redirectToRoute('app_interns');
        }
        return $this->render('intern/new.html.twig', [
            'formIntern' => $form,
            'edit' => $intern->getId()
        ]);
    }

    #[Route('intern/{id}/delete', name:'delete_intern')]
    public function delete (Intern $intern, EntityManagerInterface $entityManager) :Response
    {
        // je supprime le stagiaire et met an jour la base de données
        $entityManager->remove($intern);
        $entityManager->flush();

        return $this->redirectToRoute('app_interns');
    }

    #[Route('intern/{id}', name:'detail_intern')]
    public function detail(Intern $intern, EntityManagerInterface $entityManager) : Response
    {
        // je récupère les sessions depuis le stagiaires données
        $sessions = $intern->getSessions();
        return $this->render('intern/detail.html.twig', ['intern' => $intern, 'sessions'=> $sessions]);
    }
}
