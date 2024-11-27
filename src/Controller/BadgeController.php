<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BadgeController extends AbstractController
{
    #[Route('/badge', name: 'app_badge')]
    public function index(): Response
    {
        return $this->render('badge/badge.html.twig', [
            'controller_name' => 'BadgeController',
        ]);
    }
}
