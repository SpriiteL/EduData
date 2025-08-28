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
        try {
            $data = $request->request;

            // Validation des données
            if (!$data->get('nom') || !$data->get('prenom') || !$data->get('lieu')) {
                return new JsonResponse(['error' => 'Champs obligatoires manquants (nom, prénom, lieu)'], 400);
            }

            // Validation de la date
            $dateString = $data->get('dateTraitement');
            if (empty($dateString)) {
                return new JsonResponse(['error' => 'Date de traitement requise'], 400);
            }

            // Validation du format de date
            $dateTime = \DateTime::createFromFormat('Y-m-d', $dateString);
            if (!$dateTime) {
                return new JsonResponse(['error' => 'Format de date invalide'], 400);
            }

            // Création du badge
            $badge = new Badge();
            $badge->setNom(trim($data->get('nom')));
            $badge->setPrenom(trim($data->get('prenom')));
            $badge->setDate($dateTime);
            $badge->setClasse($data->get('classe'));
            $badge->setEtatTraitement($data->get('etatTraitement'));
            $badge->setLieu(trim($data->get('lieu')));

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
            ], 201);

        } catch (\Exception $e) {
            // Log l'erreur pour le débogage
            error_log('Erreur BadgeController::addBadge: ' . $e->getMessage());
            
            return new JsonResponse([
                'error' => 'Erreur serveur lors de l\'ajout du badge: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/badge/list', name: 'badge_list', methods: ['GET'])]
    public function list(EntityManagerInterface $entityManager): JsonResponse
    {
        try {
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

        } catch (\Exception $e) {
            error_log('Erreur BadgeController::list: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Erreur lors du chargement des badges'], 500);
        }
    }

    #[Route('/badge/delete/{id}', name: 'badge_delete', methods: ['DELETE'])]
    public function deleteBadge(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // Récupérer le badge par son ID
            $badge = $entityManager->getRepository(Badge::class)->find($id);

            if (!$badge) {
                return new JsonResponse(['error' => 'Badge non trouvé'], 404);
            }

            // Supprimer l'entité
            $entityManager->remove($badge);
            $entityManager->flush();

            return new JsonResponse(['success' => true]);

        } catch (\Exception $e) {
            error_log('Erreur BadgeController::deleteBadge: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Erreur lors de la suppression du badge'], 500);
        }
    }

    #[Route('/badge/treated', name: 'badge_treated', methods: ['GET'])]
    public function getTreatedBadges(EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $badges = $entityManager->getRepository(Badge::class)->findBy(['etatTraitement' => 'Traitée']);

            $data = [];
            foreach ($badges as $badge) {
                $data[] = [
                    'nom' => $badge->getNom(),
                    'prenom' => $badge->getPrenom(),
                    'classe' => $badge->getClasse(),
                ];
            }

            return new JsonResponse($data);

        } catch (\Exception $e) {
            error_log('Erreur BadgeController::getTreatedBadges: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Erreur lors du chargement des badges traités'], 500);
        }
    }

    #[Route('/public/badges', name: 'public_badges', methods: ['GET'])]
    public function publicDisplay(): Response
    {
        return $this->render('badge/public_badges.html.twig');
    }

    #[Route('/badge/update/{id}', name: 'badge_update', methods: ['PUT'])]
    public function updateBadge(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $badge = $entityManager->getRepository(Badge::class)->find($id);

            if (!$badge) {
                return new JsonResponse(['error' => 'Badge non trouvé'], 404);
            }

            $data = $request->request;

            // Validation des données
            if (!$data->get('nom') || !$data->get('prenom') || !$data->get('dateTraitement')) {
                return new JsonResponse(['error' => 'Champs obligatoires manquants'], 400);
            }

            // Validation du format de date
            $dateTime = \DateTime::createFromFormat('Y-m-d', $data->get('dateTraitement'));
            if (!$dateTime) {
                return new JsonResponse(['error' => 'Format de date invalide'], 400);
            }

            $badge->setNom(trim($data->get('nom')));
            $badge->setPrenom(trim($data->get('prenom')));
            $badge->setDate($dateTime);
            $badge->setClasse($data->get('classe'));
            $badge->setEtatTraitement($data->get('etatTraitement'));

            $entityManager->flush();

            return new JsonResponse(['success' => true]);

        } catch (\Exception $e) {
            error_log('Erreur BadgeController::updateBadge: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Erreur lors de la mise à jour du badge'], 500);
        }
    }
}