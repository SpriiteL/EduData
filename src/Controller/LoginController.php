<?php

namespace App\Controller;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class LoginController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Security $security, SessionInterface $session): Response
    {
        if ($this->getUser()) {
            $theme = $session->get('theme', 'theme1'); // Récupérer le thème actuel ou définir par défaut
            $session->set('theme', $theme); // S'assurer que le thème est enregistré dans la session

            if ($security->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('admin');
            } elseif ($security->isGranted('ROLE_USER')) {
                return $this->redirectToRoute('app_inventory');
            }
        }

        $theme = $session->get('theme', 'theme1');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'theme' => $theme,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(SessionInterface $session): void
    {
        // Sauvegarder le thème avant de vider la session
        $theme = $session->get('theme', 'theme1');
        $session->set('theme', $theme);

        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
