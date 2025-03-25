<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class StagiairesController extends AbstractController
{
    #[Route('/stagiaires', name: 'app_stagiaires')]
    public function index(): Response
    {
        return $this->render('intern/index.html.twig', [
            'controller_name' => 'StagiairesController',
        ]);
    }
}
