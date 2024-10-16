<?php

// src/Controller/DashboardController.php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(UserRepository $userRepository): Response
    {
        // Récupérer tous les utilisateurs depuis la base de données
        $users = $userRepository->findAll();

        return $this->render('dashboard/dashboard.html.twig', [
            'controller_name' => 'DashboardController',
            'users' => $users,
        ]);
    }
}
?>