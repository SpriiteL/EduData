<?php
// src/Controller/InventaireController.php
namespace App\Controller;

use App\Entity\Inventory;
use Doctrine\Persistence\ManagerRegistry;
use League\Csv\Writer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            }

            $entityManager->flush();

            $this->addFlash('success', 'Données importées avec succès.');
        } else {
            $this->addFlash('error', 'Veuillez télécharger un fichier.');
        }

        return $this->redirectToRoute('app_inventaire');
    }

    #[Route('/inventaire/export', name: 'app_inventaire_export', methods: ['GET'])]
    public function export(ManagerRegistry $doctrine): Response
    {
        $inventories = $doctrine->getRepository(Inventory::class)->findAll();

        $csv = Writer::createFromString('');
        $csv->insertOne(['ID', 'Type Actif', 'Prix', 'Numéro de Série Produit', 'Total Lot Produit', 'Fournisseur', 'Date d\'Entrée', 'Numéro de Série', 'Numéro de Facture Interne', 'Numéro de Facture']);

        foreach ($inventories as $inventory) {
            $csv->insertOne([
                $inventory->getId(),
                $inventory->getActiveType(),
                $inventory->getPrice(),
                $inventory->getNumProductSerie(),
                $inventory->getTotalProductLot(),
                $inventory->getProvider(),
                $inventory->getDateEntry()->format('Y-m-d'),
                $inventory->getNumSerie(),
                $inventory->getNumInvoiceIntern(),
                $inventory->getNumInvoice(),
            ]);
        }

        $response = new Response("\xEF\xBB\xBF" . $csv->toString());
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="inventaire.csv"');

        return $response;
    }
}