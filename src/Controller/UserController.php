<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/user/new', name:'new_user')]
    #[Route('/user/edit/{id}', name:'edit_user')]
    public function new_edit (User $user = null, Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager) : Response 
    {
        if (!$user){
            $user = new User();
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user');
        }
        return $this->render('user/new.html.twig', ['formUser'=> $form, 'user' =>$user, 'edit' => $user->getId()]);
    }


    #[Route('/user/delete/{id}', name:'delete_user')]
    public function delete (User $user, EntityManagerInterface $entityManager) : Response
    {
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_user');
    }

    #[Route('/user/{id}', name:'detail_user')]
    public function detail (User $user) :Response
    {
        return $this->render('user/detail.html.twig', ['user' => $user]);
    }
}
