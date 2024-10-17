<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SettingController extends AbstractController
{
    #[Route('/setting', name: 'app_setting')]
    public function index(SessionInterface $session): Response
    {
        $currentTheme = $session->get('theme', 'theme1');
        return $this->render('setting/setting.html.twig', [
            'controller_name' => 'SettingController',
            'current_theme' => $currentTheme,
        ]);
    }

    #[Route('/setting/change-theme', name: 'change_theme', methods: ['POST'])]
    public function changeTheme(Request $request, SessionInterface $session): Response
    {
        $theme = $request->request->get('theme', 'theme1');
        $session->set('theme', $theme); // Sauvegarder le thème dans la session

        return $this->redirectToRoute('app_setting');
    }
}
