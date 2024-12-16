<?php

namespace App\Controller;

use App\Entity\Badge;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BadgeController extends AbstractController
{
    #[Route('/badge', name: 'app_badge')]
    public function index(): Response
    {
        return $this->render('badge/badge.html.twig');
    }

    #[Route('/badge/add', name: 'badge_add', methods: ['POST'])]
    public function addBadge(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = $request->request;

        // Création du badge
        $badge = new Badge();
        $badge->setNom($data->get('nom'));
        $badge->setPrenom($data->get('prenom'));
        $badge->setDate(new \DateTime($data->get('dateTraitement')));
        $badge->setClasse($data->get('classe'));
        $badge->setEtatTraitement($data->get('etatTraitement'));

        // Sauvegarde dans la base
        $entityManager->persist($badge);
        $entityManager->flush();

        // Retourne le badge ajouté
        return new JsonResponse([
            'id' => $badge->getId(),
            'nom' => $badge->getNom(),
            'prenom' => $badge->getPrenom(),
            'dateTraitement' => $badge->getDate()->format('Y-m-d'),
            'classe' => $badge->getClasse(),
            'etatTraitement' => $badge->getEtatTraitement(),
        ]);
    }

    #[Route('/badge/list', name: 'badge_list', methods: ['GET'])]
    public function list(EntityManagerInterface $entityManager): JsonResponse
    {
        $badges = $entityManager->getRepository(Badge::class)->findAll();

        // Transformer les entités en tableau JSON
        $data = [];
        foreach ($badges as $badge) {
            $data[] = [
                'id' => $badge->getId(),
                'nom' => $badge->getNom(),
                'prenom' => $badge->getPrenom(),
                'dateTraitement' => $badge->getDate()->format('Y-m-d'),
                'classe' => $badge->getClasse(),
                'etatTraitement' => $badge->getEtatTraitement(),
            ];
        }

        return new JsonResponse($data);
    }

}
