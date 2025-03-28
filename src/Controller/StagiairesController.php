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
        $interns = $internRepository->findBy([],['name'=> 'ASC']);
        return $this->render('intern/index.html.twig', [
            'interns' => $interns,
        ]);
    }

    #[Route('interns/{id}/edit', name:'edit_intern')]
    #[Route('interns/new', name:'new_intern')]
    public function new(Intern $intern = null, Request $request, EntityManagerInterface $entityManager) : Response
    {
        if (!$intern){
            $intern = new Intern();
        }
        // dd($intern);
        // je cree le formulaire puis le récupère
        $form = $this->createForm(InternType::class, $intern);
        $form->handleRequest($request);

        // je verifie que le formulaire est juste 
        if ($form->isSubmitted() && $form->isValid()){
            $intern = $form->getData();

            $entityManager->persist($intern); // c'est la prepare en PDO
            $entityManager->flush();

            return $this->redirectToRoute('app_interns');
        }
        // je renvoie sur la page des stagiaires
        return $this->render('intern/new.html.twig', [
            'formIntern' => $form,
            'edit' => $intern->getId()
        ]);
    }

    #[Route('intern/{id}/delete', name:'delete_intern')]
    public function delete (Intern $intern, EntityManagerInterface $entityManager) :Response
    {
        $entityManager->remove($intern);
        $entityManager->flush();

        return $this->redirectToRoute('app_interns');
    }

    #[Route('intern/{id}', name:'detail_intern')]
    public function detail(Intern $intern, EntityManagerInterface $entityManager) : Response
    {
        $sessions = $intern->getSessions();
        return $this->render('intern/detail.html.twig', ['intern' => $intern, 'sessions'=> $sessions]);
    }
}
