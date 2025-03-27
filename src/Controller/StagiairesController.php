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
        $interns = $internRepository->findAll();
        return $this->render('intern/index.html.twig', [
            'interns' => $interns,
        ]);
    }

    #[Route('interns/new', name:'new_intern')]
    public function new(Request $request, EntityManagerInterface $entityManager) : Response
    {
        $intern = new Intern();

        // je cree le formulaire puis le récupère
        $form = $this->createForm(InternType::class, $intern);
        // dd($form);
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
}
