<?php
// src/Controller/StatisticsController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use App\Entity\Imprimante;



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
    public function import(Request $request, EntityManagerInterface $entityManager): Response
    {
        $file = $request->files->get('file');

        if ($file && $file->isValid()) {
            try {
                $content = file_get_contents($file->getPathname());

                // Vérification de l'encodage et conversion si nécessaire
                if (mb_detect_encoding($content, 'UTF-8', true) === false) {
                    $content = mb_convert_encoding($content, 'UTF-8', 'ISO-8859-1');
                }

                // Création d'un objet CSV à partir du contenu
                $csv = Reader::createFromString($content);
                $csv->setDelimiter(';'); // Assure-toi que le séparateur est ';' dans ton fichier CSV
                $csv->setHeaderOffset(5); // L'en-tête commence à la 4ème ligne

                // Lecture des lignes
                $records = $csv->getRecords();
                $count = 0;

                // Parcours des lignes du CSV
                foreach ($records as $row) {
                    $normalizedRow = array_map(fn($value) => trim($value), $row);

                    try {
                        // Création d'une nouvelle imprimante
                        $imprimante = new Imprimante();

                        // Mapping des colonnes du CSV aux attributs de l'entité Imprimante
                        $imprimante->setUsername($normalizedRow['Owner name'] ?? '');  // Vérifie le nom exact dans le CSV
                        $imprimante->setPrinter($normalizedRow['Job Name'] ?? '');  // Vérifie le nom exact dans le CSV
                        $imprimante->setIdPrinter($normalizedRow['Job ID'] ?? '');  // Vérifie le nom exact dans le CSV
                        $imprimante->setNamePrinter($normalizedRow['Job Kind'] ?? '');  // Vérifie le nom exact dans le CSV

                        // Initialisation des compteurs de copies
                        $nbCopyBW = 0;
                        $nbCopyColor = 0;

                        // Vérification de la valeur de la colonne "Color" et incrémentation des compteurs
                        if (isset($normalizedRow['Color']) && $normalizedRow['Color'] === 'Black') {
                            $nbCopyBW += 1; // Si "Black", incrémenter le nombre de copies en noir et blanc
                        }
                        if (isset($normalizedRow['Color']) && $normalizedRow['Color'] === 'Full Color') {
                            $nbCopyColor += 1; // Si "Full Color", incrémenter le nombre de copies couleur
                        }

                        // Affectation des valeurs calculées dans l'entité
                        $imprimante->setNbCopyBW($nbCopyBW);
                        $imprimante->setNbCopyColor($nbCopyColor);

                        // Persist de l'entité dans la base de données
                        $entityManager->persist($imprimante);
                        $count++;
                    } catch (\Exception $e) {
                        // En cas d'erreur lors de l'ajout de l'enregistrement
                        $this->addFlash('error', 'Erreur lors de l\'ajout de l\'enregistrement : ' . $e->getMessage());
                    }
                }

                // Si des enregistrements ont été ajoutés, on effectue un flush dans la base de données
                if ($count > 0) {
                    $entityManager->flush();
                    $this->addFlash('success', "$count enregistrement(s) importé(s) avec succès.");
                } else {
                    $this->addFlash('error', 'Aucun enregistrement valide trouvé dans le fichier.');
                }
            } catch (\Exception $e) {
                // En cas d'erreur générale d'importation
                $this->addFlash('error', 'Erreur lors de l\'import : ' . $e->getMessage());
            }
        } else {
            $this->addFlash('error', 'Veuillez télécharger un fichier valide.');
        }

        return $this->redirectToRoute('app_imprimante');
    }





}