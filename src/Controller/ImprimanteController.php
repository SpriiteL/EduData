<?php
// src/Controller/StatisticsController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;

class ImprimanteController extends AbstractController
{
    #[Route('/imprimante', name: 'app_imprimante')]
    public function index(UserRepository $userRepository): Response
    {
        $admins = $userRepository->count(['roles' => 'ROLE_ADMIN']);
        $users = $userRepository->count(['roles' => 'ROLE_USER']);

        return $this->render('imprimante/imprimante.html.twig', [
            'adminCount' => $admins,
            'userCount' => $users,
        ]);
    }

    #[Route('/imprimante/import', name: 'app_imprimante_import', methods: ['POST'])]
    public function import(Request $request, ManagerRegistry $doctrine): Response
    {
        $file = $request->files->get('file');
        if ($file && $file->isValid()) {
            try {
                $content = file_get_contents($file->getPathname());
                if (mb_detect_encoding($content, 'UTF-8', true) === false) {
                    $content = mb_convert_encoding($content, 'UTF-8', 'ISO-8859-1');
                }

                $csv = Reader::createFromString($content);
                $csv->setDelimiter(';');
                $csv->setHeaderOffset(0);

                $records = $csv->getRecords();
                $entityManager = $doctrine->getManager();

                $requiredColumns = [
                    "Username",
                    "Printer",
                    "Nb Copy B W",
                    "Nb Copy Color",
                    "Id Printer",
                    "Name Printer",
                ];

                $count = 0;
                foreach ($records as $row) {
                    $normalizedRow = array_map(fn($value) => trim($value), $row);

                    foreach ($requiredColumns as $column) {
                        if (!isset($normalizedRow[$column])) {
                            $this->addFlash('error', "Colonne manquante : $column");
                            continue 2;
                        }
                    }

                    $imprimante = new Imprimante();
                    try {
                        $imprimante->setUsername($normalizedRow["Type d'Actif"]);
                        $imprimante->setProvider($normalizedRow['Fournisseur']);
                        $imprimante->setDateEntry(new \DateTime($normalizedRow["Date d'arrivée"]));
                        $imprimante->setNumSerie($normalizedRow['Numéro de Série']);
                        $imprimante->setNumInvoiceIntern($normalizedRow['Numéro Facture Interne']);
                        $imprimante->setNumInvoice($normalizedRow['Numéro de Facture']);

                        $entityManager->persist($imprimante);
                        $count++;
                    } catch (\Exception $e) {
                        $this->addFlash('error', 'Erreur lors de l\'ajout de l\'enregistrement : ' . $e->getMessage());
                    }
                }

                if ($count > 0) {
                    $entityManager->flush();
                    $this->addFlash('success', "$count enregistrement(s) importé(s) avec succès.");
                } else {
                    $this->addFlash('error', 'Aucun enregistrement valide trouvé dans le fichier.');
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de l\'import : ' . $e->getMessage());
            }
        } else {
            $this->addFlash('error', 'Veuillez télécharger un fichier valide.');
        }

        return $this->redirectToRoute('app_imprimante');
    }
}