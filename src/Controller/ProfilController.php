<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(): Response
    {
        $users = $this->getUser();

        return $this->render('profil/profil.html.twig', [
            'controller_name' => 'ProfilController',
            'users' => $users,
        ]);
    }
}
