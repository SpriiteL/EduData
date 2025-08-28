<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Etablishment;
use App\Repository\UserRepository;
use App\Repository\EtablishmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/gestion/user/admin')]
final class GestionUserAdminController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private EtablishmentRepository $etablishmentRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'app_gestion_user_admin')]
    public function index(): Response
    {
        return $this->render('gestion_user_admin/index.html.twig', [
            'controller_name' => 'GestionUserAdminController',
        ]);
    }

    #[Route('/list', name: 'app_user_list', methods: ['GET'])]
    public function listUsers(): JsonResponse
    {
        try {
            $users = $this->userRepository->findAll();
            $usersData = [];

            foreach ($users as $user) {
                $usersData[] = [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail(),
                    'firstname' => $user->getFirstname(),
                    'lastname' => $user->getLastname(),
                    'roles' => $user->getRoles(),
                    'etablishment' => $user->getEtablishment() ? [
                        'id' => $user->getEtablishment()->getId(),
                        'name' => $user->getEtablishment()->getName() // Assumant qu'il y a une méthode getName()
                    ] : null
                ];
            }

            return new JsonResponse($usersData);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors du chargement des utilisateurs'], 500);
        }
    }

    #[Route('/etablishments', name: 'app_etablishments_list', methods: ['GET'])]
    public function listEtablishments(): JsonResponse
    {
        try {
            $etablishments = $this->etablishmentRepository->findAll();
            $etablishmentsData = [];

            foreach ($etablishments as $etablishment) {
                $etablishmentsData[] = [
                    'id' => $etablishment->getId(),
                    'name' => $etablishment->getName() // Assumant qu'il y a une méthode getName()
                ];
            }

            return new JsonResponse($etablishmentsData);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors du chargement des établissements'], 500);
        }
    }

    #[Route('/add', name: 'app_user_add', methods: ['POST'])]
    public function addUser(Request $request): JsonResponse
    {
        try {
            $data = $request->request->all();

            // Validation des champs requis
            if (empty($data['username']) || empty($data['email']) || empty($data['firstname']) || 
                empty($data['lastname']) || empty($data['password'])) {
                return new JsonResponse(['error' => 'Tous les champs sont obligatoires'], 400);
            }

            // Vérification si l'utilisateur existe déjà
            $existingUser = $this->userRepository->findOneBy(['username' => $data['username']]);
            if ($existingUser) {
                return new JsonResponse(['error' => 'Ce nom d\'utilisateur est déjà utilisé'], 400);
            }

            $existingEmail = $this->userRepository->findOneBy(['email' => $data['email']]);
            if ($existingEmail) {
                return new JsonResponse(['error' => 'Cette adresse email est déjà utilisée'], 400);
            }

            $user = new User();
            $user->setUsername($data['username']);
            $user->setEmail($data['email']);
            $user->setFirstname($data['firstname']);
            $user->setLastname($data['lastname']);

            // Hash du mot de passe
            $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);

            // Gestion des rôles
            $roles = isset($data['roles']) ? $data['roles'] : ['ROLE_USER'];
            if (is_string($roles)) {
                $roles = [$roles];
            }
            $user->setRoles($roles);

            // Gestion de l'établissement
            if (!empty($data['etablishment_id'])) {
                $etablishment = $this->etablishmentRepository->find($data['etablishment_id']);
                if ($etablishment) {
                    $user->setEtablishment($etablishment);
                }
            }

            // Validation
            $errors = $this->validator->validate($user);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }
                return new JsonResponse(['error' => implode(', ', $errorMessages)], 400);
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return new JsonResponse([
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'roles' => $user->getRoles(),
                'etablishment' => $user->getEtablishment() ? [
                    'id' => $user->getEtablishment()->getId(),
                    'name' => $user->getEtablishment()->getName()
                ] : null
            ]);

        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de l\'ajout de l\'utilisateur: ' . $e->getMessage()], 500);
        }
    }

    #[Route('/update-password/{id}', name: 'app_user_update_password', methods: ['POST'])]
    public function updatePassword(int $id, Request $request): JsonResponse
    {
        try {
            $user = $this->userRepository->find($id);
            if (!$user) {
                return new JsonResponse(['error' => 'Utilisateur non trouvé'], 404);
            }

            $data = $request->request->all();
            if (empty($data['password'])) {
                return new JsonResponse(['error' => 'Le mot de passe est obligatoire'], 400);
            }

            if (strlen($data['password']) < 6) {
                return new JsonResponse(['error' => 'Le mot de passe doit contenir au moins 6 caractères'], 400);
            }

            // Hash du nouveau mot de passe
            $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);

            $this->entityManager->flush();

            return new JsonResponse(['success' => true, 'message' => 'Mot de passe mis à jour avec succès']);

        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la mise à jour du mot de passe'], 500);
        }
    }

    #[Route('/delete/{id}', name: 'app_user_delete', methods: ['DELETE'])]
    public function deleteUser(int $id): JsonResponse
    {
        try {
            $user = $this->userRepository->find($id);
            if (!$user) {
                return new JsonResponse(['error' => 'Utilisateur non trouvé'], 404);
            }

            // Vérifier que ce n'est pas l'utilisateur actuel
            if ($user === $this->getUser()) {
                return new JsonResponse(['error' => 'Vous ne pouvez pas supprimer votre propre compte'], 400);
            }

            $this->entityManager->remove($user);
            $this->entityManager->flush();

            return new JsonResponse(['success' => true, 'message' => 'Utilisateur supprimé avec succès']);

        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la suppression de l\'utilisateur'], 500);
        }
    }
}