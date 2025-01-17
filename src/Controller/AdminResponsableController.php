<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminResponsableController extends AbstractController
{
    #[Route('/admin/responsable', name: 'app_admin_responsable')]
    public function index(UserRepository $userRepository): Response
    {
        // Récupération de tous les utilisateurs
        $users = $userRepository->findAll();

        return $this->render('admin_responsable/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/responsable/update-role/{id}', name: 'update_user_role', methods: ['POST'])]
    public function updateRole(
        User $user,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        // Vérification du rôle envoyé
        $newRole = $request->request->get('roles');
        if ($newRole && in_array($newRole, ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_RESPONSABLE'])) {
            $user->setRoles([$newRole]);
            $entityManager->flush();

            $this->addFlash('success', 'Rôle mis à jour avec succès.');
        } else {
            $this->addFlash('error', 'Rôle invalide.');
        }

        return $this->redirectToRoute('app_admin_responsable');
    }
}
