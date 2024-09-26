<?php
// src/Controller/InventaireController.php
namespace App\Controller;

use App\Entity\Inventory;
use Doctrine\Persistence\ManagerRegistry;
use League\Csv\Reader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InventaireController extends AbstractController
{
    #[Route('/inventaire', name: 'app_inventaire')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $inventories = $doctrine->getRepository(Inventory::class)->findAll();

        return $this->render('inventaire/index.html.twig', [
            'controller_name' => 'InventaireController',
            'inventories' => $inventories,
        ]);
    }

    #[Route('/inventaire/import', name: 'app_inventaire_import', methods: ['POST'])]
    public function import(Request $request, ManagerRegistry $doctrine): Response
    {
        $file = $request->files->get('file');
        if ($file) {
            $csv = Reader::createFromPath($file->getPathname(), 'r');
            $csv->setHeaderOffset(0);
            $records = $csv->getRecords();

            $entityManager = $doctrine->getManager();

            foreach ($records as $row) {
                // Debugging: Log the row data
                dump($row);

                // Check if all required keys exist in the row
                $requiredKeys = ['activeType', 'price', 'numProductSerie', 'totalProductLot', 'provider', 'dateEntry', 'numSerie', 'numInvoiceIntern'];
                $missingKeys = array_diff($requiredKeys, array_keys($row));

                if (!empty($missingKeys)) {
                    // Log the missing keys and skip this row
                    dump('Missing keys: ' . implode(', ', $missingKeys));
                    continue;
                }

                $inventory = new Inventory();
                $inventory->setActiveType($row['activeType']);
                $inventory->setPrice($row['price']);
                $inventory->setNumProductSerie($row['numProductSerie']);
                $inventory->setTotalProductLot($row['totalProductLot']);
                $inventory->setProvider($row['provider']);
                $inventory->setDateEntry(new \DateTime($row['dateEntry']));
                $inventory->setNumSerie($row['numSerie']);
                $inventory->setNumInvoiceIntern($row['numInvoiceIntern']);
                $inventory->setNumInvoice($row['numInvoice']);

                $entityManager->persist($inventory);

                // Debugging: Log the inventory entity
                dump($inventory);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Données importées avec succès.');
        } else {
            $this->addFlash('error', 'Veuillez télécharger un fichier.');
        }

        return $this->redirectToRoute('app_inventaire');
    }
}