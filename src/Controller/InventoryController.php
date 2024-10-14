<?php
// src/Controller/inventoryController.php
namespace App\Controller;

use App\Entity\Inventory;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use League\Csv\Writer;
use League\Csv\Exception;
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
            try {
                $csv = Reader::createFromPath($file->getPathname(), 'r');
                $csv->setDelimiter(';'); // Spécifie le séparateur
                $csv->setHeaderOffset(0);
                $records = $csv->getRecords();

                $entityManager = $doctrine->getManager();

                foreach ($records as $row) {
                    $inventory = new Inventory();
                    $inventory->setActiveType($row["Type d'Actif"]);
                    $inventory->setProvider($row['Fournisseur']);
                    $inventory->setDateEntry(new \DateTime($row["Date d'arrivée"]));
                    $inventory->setNumSerie($row['Numéro de Série']);
                    $inventory->setNumInvoiceIntern($row['Numéro Facture Interne']);
                    $inventory->setNumInvoice($row['Numéro de Facture']);
                    $inventory->setPrice($row['Prix Neuf']);
                    $inventory->setNumProductSerie($row['Numero de produit de la série']);
                    $inventory->setTotalProductLot($row['Nombre total de produits dans le lot']);

                    $entityManager->persist($inventory);
                }

                $entityManager->flush();
                $this->addFlash('success', 'Données importées avec succès.');
            } catch (Exception $e) {
                $this->addFlash('error', 'Erreur lors de l\'import : ' . $e->getMessage());
            }
        } else {
            $this->addFlash('error', 'Veuillez télécharger un fichier.');
        }

        return $this->redirectToRoute('app_inventory');
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
                // $inventory->getName(),
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

    #[Route('/inventory/delete/{id}', name: 'app_inventory_delete', methods: ['POST'])]
    public function delete(Request $request, Inventory $inventory, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$inventory->getId(), $request->request->get('_token'))) {
            $entityManager->remove($inventory);
            $entityManager->flush();

            $this->addFlash('success', 'L\'élément a été supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('app_inventory');
    }
}
