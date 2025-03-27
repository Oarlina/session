<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CategoriesController extends AbstractController
{
    #[Route('/categories', name: 'app_categories')]
    public function index(CategoryRepository $catagoriesRepository): Response
    {
        $categories = $catagoriesRepository->findAll();
        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }
    #[Route('/categories/{id}/edit', name:'edit_category')]
    #[Route('/categories/new', name:'new_category')]
    public function new (Category $category = null, Request $request, EntityManagerInterface $entityManager) : Response
    {
        if (!$category){
            $category = new Category();
        }

        // on cree un formulaire 
        $form = $this->createForm(CategoryType::class, $category);
        // on dit que l'on veut traiter le formulaire 
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()){
            $category = $form->getData();

            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('app_categories');
        }
        return $this->render('category/new.html.twig', [
            'formCategory' => $form,
            'edit' => $category->getId()
        ]);
    }

    #[Route('/categories/{id}/delete', name:'delete_category')]
    public function delete (Category $category, EntitymanagerInterface $entityManager) : Response
    {
        $entityManager->remove($category);
        $entityManager->flush();

        return $this->redirectToRoute('app_categories');
    }

    #[Route('/category/{id}/detail', name:'detail_category')]
    public function detail(Category $category) : Response
    {
        return $this->render('category/detail.html.twig', ['category' => $category]);
    }
}
