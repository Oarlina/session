<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use App\Repository\SessionRepository;
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
        // je récupère tous les utilisateurs et lies trie dans l'ordre croissant par leur prenom
        $users = $userRepository->findBy([], ['name' => 'ASC']);
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/user/new', name:'new_user')]
    #[Route('/user/edit/{id}', name:'edit_user')]
    public function new_edit (User $user = null, Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager) : Response 
    {
        // si l'utilisateur n'est pas données en paramètre alors je crée un nouvel utilisateur
        if (!$user){
            $user = new User();
        }
        // on crée un formulaire
        $form = $this->createForm(UserType::class, $user);
        // on dit que l'on veut traiter le formulaire 
        $form->handleRequest($request);
        // si le formulaire est valide et envoyer
        if ($form->isSubmitted() && $form->isValid())
        {
            // on récupère les données
            $user = $form->getData();
            // puis on fait un 'prepare' puis 'query' afin de mettre a jour la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user');
        }
        return $this->render('user/new.html.twig', ['formUser'=> $form, 'user' =>$user, 'edit' => $user->getId()]);
    }


    #[Route('/user/delete/{id}', name:'delete_user')]
    public function delete (User $user, EntityManagerInterface $entityManager) : Response
    {
        // je supprime l'utilisateur puis m!et a jour la base de données
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_user');
    }

    #[Route('/user/{id}', name:'detail_user')]
    public function detail (User $user, SessionRepository  $sessionRepository) :Response
    {
        // je récupère les sessions auquel l'utilisateurt doit participer et je les tri dans l'ordre croissant par laa date de début de la session puis par la date de fin
        $sessions = $sessionRepository->findBy(['user' => $user], ['beginSession' => 'ASC', 'finishSession' => 'ASC']);
        return $this->render('user/detail.html.twig', ['user' => $user, 'sessions' =>$sessions]);
    }
}
