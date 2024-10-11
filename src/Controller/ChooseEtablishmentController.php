<?php
// src/Controller/ChooseEtablishmentController.php

namespace App\Controller;

use App\Entity\Etablishment;
use App\Entity\User;
use App\Form\EtablishmentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChooseEtablishmentController extends AbstractController
{
    #[Route('/choose-etablishment', name: 'app_choose_etablishment')]
    public function chooseEtablishment(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            $etablishmentId = $request->request->get('etablishment_id');
            if ($etablishmentId === 'new') {
                return $this->redirectToRoute('app_create_etablishment');
            } else {
                $etablishment = $entityManager->getRepository(Etablishment::class)->find($etablishmentId);
                $user->setEtablishment($etablishment);
                $entityManager->flush();
                return $this->redirectToRoute('app_inventory');
            }
        }

        $etablishments = $entityManager->getRepository(Etablishment::class)->findAll();

        return $this->render('choose_etablishment/choose.html.twig', [
            'etablishments' => $etablishments,
        ]);
    }

    #[Route('/create-etablishment', name: 'app_create_etablishment')]
    public function createEtablishment(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $etablishment = new Etablishment();
        $form = $this->createForm(EtablishmentType::class, $etablishment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($etablishment);
            $user->setEtablishment($etablishment);
            $user->setRoles(['ROLE_DIRECTOR']);
            $entityManager->flush();
            return $this->redirectToRoute('app_inventory');
        }

        return $this->render('choose_etablishment/create.html.twig', [
            'etablishmentForm' => $form->createView(),
        ]);
    }
}