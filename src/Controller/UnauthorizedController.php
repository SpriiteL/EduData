<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UnauthorizedController extends AbstractController
{
    #[Route('/unauthorized', name: 'app_unauthorized')]
    public function index(): Response
    {
        return $this->render('unauthorized/index.html.twig', [
            'controller_name' => 'UnauthorizedController',
        ]);
    }
}
