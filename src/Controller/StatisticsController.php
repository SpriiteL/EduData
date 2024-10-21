<?php
// src/Controller/StatisticsController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;

class StatisticsController extends AbstractController
{
    #[Route('/statistics', name: 'app_statistics')]
    public function index(UserRepository $userRepository): Response
    {
        $admins = $userRepository->count(['roles' => 'ROLE_ADMIN']);
        $users = $userRepository->count(['roles' => 'ROLE_USER']);

        return $this->render('statistics/stat.html.twig', [
            'adminCount' => $admins,
            'userCount' => $users,
        ]);
    }
}