<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CourseRepository;
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
        // je récupère les catégorie dans l'ordre croissant de leur nom
        $categories = $catagoriesRepository->findBy([], ['nameCategory' => 'ASC']);
        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }
    #[Route('/categories/{id}/edit', name:'edit_category')]
    #[Route('/categories/new', name:'new_category')]
    public function new (Category $category = null, Request $request, EntityManagerInterface $entityManager) : Response
    {
        // si la catégorie n'est pas donné en praramètre alors je crée une nouvelle catégorie
        if (!$category){
            $category = new Category();
        }

        // on crée un formulaire 
        $form = $this->createForm(CategoryType::class, $category);
        // on dit que l'on veut traiter le formulaire 
        $form->handleRequest($request);
        // si le formulaire est valide et envoyer
        if ($form->isSubmitted() && $form->isValid()){
            // on récupère les données
            $category = $form->getData();
            // puis on fait un 'prepare' puis 'query' afin de mettre a jour la base de données
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('app_categories');
        }
        return $this->render('category/new.html.twig', [
            'formCategory' => $form,
            'edit' => $category->getId() // pour modifier le titre de la page
        ]);
    }

    #[Route('/categories/{id}/delete', name:'delete_category')]
    public function delete (Category $category, EntitymanagerInterface $entityManager) : Response
    {
        // on supprime la catégorie et met a jour la base de données
        $entityManager->remove($category);
        $entityManager->flush();

        return $this->redirectToRoute('app_categories');
    }

    #[Route('/category/{id}/detail', name:'detail_category')]
    public function detail(Category $category, CategoryRepository $categoryRepository, CourseRepository $courseRepository) : Response
    {
        // on cherche les modules de cette catégorie et les trie dans l'ordre croissant par leur nom
        $courses = $courseRepository->findBy(['category'=> $category->getId()], ['nameCourse' => 'ASC']);
        return $this->render('category/detail.html.twig', ['category'=> $category, 'courses' => $courses]);
    }
}
