<?php
// src/Controller/inventoryController.php
namespace App\Controller;

use App\Entity\Inventory;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use League\Csv\Writer;
use League\Csv\Reader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InventoryController extends AbstractController
{
    #[Route('/inventory', name: 'app_inventory')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $etablishment = $user->getEtablishment();

        $inventories = $entityManager->getRepository(Inventory::class)->findBy(['etablishment' => $etablishment]);

        return $this->render('inventory/inventory.html.twig', [
            'inventories' => $inventories,
        ]);
    }

    #[Route('/inventory/import', name: 'app_inventory_import', methods: ['POST'])]
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
                $inventory->setName($row['name']);
                $inventory->setActiveType($row['activeType']);
                $inventory->setProvider($row['provider']);
                $inventory->setDateEntry(new \DateTime($row['dateEntry']));
                $inventory->setNumSerie($row['numSerie']);
                $inventory->setNumInvoiceIntern($row['numInvoiceIntern']);
                $inventory->setNumInvoice($row['numInvoice']);
                $inventory->setPrice($row['price']);
                $inventory->setNumProductSerie($row['numProductSerie']);
                $inventory->setTotalProductLot($row['totalProductLot']);

                $entityManager->persist($inventory);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Données importées avec succès.');
        } else {
            $this->addFlash('error', 'Veuillez télécharger un fichier.');
        }

        return $this->redirectToRoute('app_inventory');

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
    }

    #[Route('/inventory/export', name: 'app_inventory_export', methods: ['GET'])]
    public function export(ManagerRegistry $doctrine): Response
    {
        $inventories = $doctrine->getRepository(Inventory::class)->findAll();
        $csv = Writer::createFromString('');
        $csv->insertOne(['ID', 'Nom', 'Type Actif', 'Fournisseur', 'Date d\'Entrée', 'Numéro de Série', 'Numéro Facture Interne', 'Numéro de Facture', 'Prix', 'Numéro de Produit Série', 'Total de Produit Lot']);
        foreach ($inventories as $inventory) {
            $csv->insertOne([
                $inventory->getId(),
                $inventory->getName(),
                $inventory->getActiveType(),
                $inventory->getProvider(),
                $inventory->getDateEntry()->format('Y-m-d'),
                $inventory->getNumSerie(),
                $inventory->getNumInvoiceIntern(),
                $inventory->getNumInvoice(),
                $inventory->getPrice(),
                $inventory->getNumProductSerie(),
                $inventory->getTotalProductLot(),
            ]);
        }
        $response = new Response("\xEF\xBB\xBF" . $csv->toString());
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="inventory.csv"');
        return $response;

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

    }
}
