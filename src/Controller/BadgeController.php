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
        $badge->setLieu($data->get('lieu'));

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
            'lieu' => $badge->getLieu(),
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
                'lieu' => $badge->getLieu(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/badge/delete/{id}', name: 'badge_delete', methods: ['DELETE'])]
    public function deleteBadge(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupérer le badge par son ID
        $badge = $entityManager->getRepository(Badge::class)->find($id);

        if (!$badge) {
            return new JsonResponse(['error' => 'Badge non trouvé'], 404);
        }

        // Supprimer l'entité
        $entityManager->remove($badge);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/badge/treated', name: 'badge_treated', methods: ['GET'])]
    public function getTreatedBadges(EntityManagerInterface $entityManager): JsonResponse
    {
        $badges = $entityManager->getRepository(Badge::class)->findBy(['etatTraitement' => 'Traitée']);

        $data = [];
        foreach ($badges as $badge) {
            $data[] = [
                'nom' => $badge->getNom(),
                'prenom' => $badge->getPrenom(),
                'classe' => $badge->getClasse(),
                'lieu' => $badge->getLieu(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/public/badges', name: 'public_badges', methods: ['GET'])]
    public function publicDisplay(): Response
    {
        return $this->render('badge/public_badges.html.twig');
    }

    #[Route('/badge/update/{id}', name: 'badge_update', methods: ['PUT'])]
    public function updateBadge(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $badge = $entityManager->getRepository(Badge::class)->find($id);

        if (!$badge) {
            return new JsonResponse(['error' => 'Badge non trouvé'], 404);
        }

        $data = $request->request;

        $badge->setNom($data->get('nom'));
        $badge->setPrenom($data->get('prenom'));
        $badge->setDate(new \DateTime($data->get('dateTraitement')));
        $badge->setClasse($data->get('classe'));
        $badge->setEtatTraitement($data->get('etatTraitement'));
        $badge->setLieu($data->get('lieu'));

        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }


}
