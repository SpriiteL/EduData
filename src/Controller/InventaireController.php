<?php

namespace App\Controller;

use App\Entity\Inventory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;

class InventaireController extends AbstractController
{
    #[Route('/inventaire', name: 'app_inventaire')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $inventories = $doctrine->getRepository(Inventory::class)->findAll();

        return $this->render('inventaire/index.html.twig', [
            'controller_name' => 'InventaireController',
            'inventories' => $inventories,
        ]);
    }
}
