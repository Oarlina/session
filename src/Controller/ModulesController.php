<?php

namespace App\Controller;

use App\Entity\Course;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ModulesController extends AbstractController
{
    #[Route('/courses', name: 'app_courses')]
    public function index(CourseRepository $courseRepository): Response
    {
        // je récupère les modules dans l'ordre croissant de leur nom
        $courses = $courseRepository->findAll([], ['nameCategory' => 'ASC']);
        return $this->render('course/index.html.twig', [
            'courses' => $courses
        ]);
    }

    #[Route('/course/{course}/edit', name:'edit_course')]
    #[Route('/course/{idCategory}/new', name:'new_course')]
    public function new(int $idCategory =null, Course $course =null, Request $request, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository) : Response
    {
        // si la catégorie n'est pas donné en praramètre alors je crée une nouvelle catégorie
        if( !$course){
            $course = new Course();
        }
        // si la catégorie n'est pas donné en praramètre alors je récupère l'id de la catégorie 
        if (!$idCategory){
            $idCategory = $course->getCategory()->getId();
        }
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);
        // je cherche dans category repository l'id que j'ai envoyer dans la route 
        $category = $categoryRepository->find($idCategory); 

        if ($form->isSubmitted() && $form->isValid()){
            $course = $form->getData();
            $course->setCategory($category); // je rajoute l'id de la catégorie 

            $entityManager->persist($course); // c'est la prepare en PDO
            $entityManager->flush(); // c'est l'execute en PDO

            // pour le return je dois passer un tableau qui va donner l'id de la category ou j'ai ajouter le module
            return $this->redirectToRoute('detail_category', array('id' => $category->getId()));
        } 
        return $this->render('course/new.html.twig', [
            'formCourse' => $form,
            'category' => $category,
            'edit' => $course->getId()
        ]);
    }
    

    #[Route('/course/{id}/delete', name:'delete_course')]
    public function delete(Course $course, EntityManagerInterface $entityManager): Response
    {
        // je récupère l'id de la category depuis les modules
        $categoryId = $course->getCategory()->getId();
        // je supprime le module et met à jour la base de données
        $entityManager->remove($course);
        $entityManager->flush();
        
        return $this->redirectToRoute('detail_category', array('id' => $categoryId));
    }
}
