<?php

namespace App\Controller;


use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Security $security): Response
    {
        if ($this->getUser()) {
            // return $this->redirectToRoute('teacher_dashboard');
            if ($security->isGranted('ROLE_ADMIN')) {
                // Redirigez vers la page d'administration
                return $this->redirectToRoute('admin'); // Remplacez par votre route d'administration
            } elseif ($security->isGranted('ROLE_USER')) {
                // Redirigez vers la page utilisateur
                return $this->redirectToRoute('app_dashboard'); // Remplacez par votre route utilisateur
            }
        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
