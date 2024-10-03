<?php
// src/Controller/inventoryController.php
namespace App\Controller;

use App\Entity\Inventory;
use App\Entity\User;
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
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $search = $request->query->get('search', '');
        $status = $request->query->get('status', '');

        $repository = $doctrine->getRepository(Inventory::class);
        $queryBuilder = $repository->createQueryBuilder('i');

        if ($search) {
            $queryBuilder->andWhere('i.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($status !== '') {
            $queryBuilder->andWhere('i.statut = :status')
                ->setParameter('status', $status);
        }

        $inventories = $queryBuilder->getQuery()->getResult();

        return $this->render('inventory/inventory.html.twig', [
            'controller_name' => 'InventoryController',
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
        $csv->insertOne(['ID', 'Nom', 'Type Actif', 'Prix', 'Numéro de Série Produit', 'Total Lot Produit', 'Fournisseur', 'Date d\'Entrée', 'Numéro de Série', 'Numéro de Facture Interne', 'Numéro de Facture']);
        foreach ($inventories as $inventory) {
            $csv->insertOne([
                $inventory->getId(),
                $inventory->getName(),
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
        $response->headers->set('Content-Disposition', 'attachment; filename="inventory.csv"');
        return $response;

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

    }
}
