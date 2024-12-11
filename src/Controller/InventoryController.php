<?php
// src/Controller/InventoryController.php
namespace App\Controller;

use App\Entity\Inventory;
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
    public function index(ManagerRegistry $doctrine): Response
    {
        $inventories = $doctrine->getRepository(Inventory::class)->findAll();

        return $this->render('inventory/inventory.html.twig', [
            'inventories' => $inventories,
        ]);
    }

    #[Route('/inventory/import', name: 'app_inventory_import', methods: ['POST'])]
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
                    "Type d'Actif", "Fournisseur", "Date d'arrivée",
                    "Numéro de Série", "Numéro Facture Interne",
                    "Numéro de Facture", "Prix Neuf",
                    "Numero de produit de la série",
                    "Nombre total de produits dans le lot", "Nom de la Salle"
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

                    $inventory = new Inventory();
                    try {
                        // Génération d'un tag RFID unique
                        $inventory->setReference($this->generateRFID());
                        $inventory->setActiveType($normalizedRow["Type d'Actif"]);
                        $inventory->setProvider($normalizedRow['Fournisseur']);
                        $inventory->setDateEntry(new \DateTime($normalizedRow["Date d'arrivée"]));
                        $inventory->setNumSerie($normalizedRow['Numéro de Série']);
                        $inventory->setNumInvoiceIntern($normalizedRow['Numéro Facture Interne']);
                        $inventory->setNumInvoice($normalizedRow['Numéro de Facture']);
                        $inventory->setPrice(floatval($normalizedRow['Prix Neuf']));
                        $inventory->setNumProductSerie($normalizedRow['Numero de produit de la série']);
                        $inventory->setTotalProductLot(intval($normalizedRow['Nombre total de produits dans le lot']));
                        $inventory->setNameRoom($normalizedRow['Nom de la Salle']);

                        $entityManager->persist($inventory);
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

        return $this->redirectToRoute('app_inventory');
    }

    #[Route('/inventory/export', name: 'app_inventory_export', methods: ['GET'])]
    public function export(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $inventories = $entityManager->getRepository(Inventory::class)->findAll();

        $csvFileName = 'inventaire_' . date('Ymd') . '.csv';
        $outputBuffer = fopen('php://temp', 'r+');

        fwrite($outputBuffer, "\xEF\xBB\xBF");

        fputcsv($outputBuffer, [
            "Référence",
            "Type d'Actif", 
            "Fournisseur", 
            "Date d'arrivée", 
            "Numéro de Série", 
            "Numéro Facture Interne", 
            "Numéro de Facture", 
            "Prix Neuf", 
            "Numero de produit de la série", 
            "Nombre total de produits dans le lot",
            "Nom de la Salle"
        ], ';');

        foreach ($inventories as $inventory) {
            $totalProducts = $inventory->getTotalProductLot();
            $numProductSerieBase = $inventory->getNumProductSerie();

            // Écrire une ligne pour chaque produit dans le lot
            for ($i = 0; $i < $totalProducts; $i++) {
                fputcsv($outputBuffer, [
                    $inventory->getReference(),
                    $inventory->getActiveType(),
                    $inventory->getProvider(),
                    $inventory->getDateEntry()->format('Y-m-d'), // Formater la date si nécessaire
                    $inventory->getNumSerie(),
                    $inventory->getNumInvoiceIntern(),
                    $inventory->getNumInvoice(),
                    $inventory->getPrice(),
                    $numProductSerieBase + $i, // Incrémenter numProductSerie
                    $totalProducts, // Utiliser le total des produits dans le lot
                    $inventory->getNameRoom()
                ], ';'); // Utiliser le point-virgule comme séparateur
            }
        }
        rewind($outputBuffer);

        $response = new Response(stream_get_contents($outputBuffer));
        fclose($outputBuffer);

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $csvFileName . '"');

        return $response;
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

    private function generateRFID(): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $rfid = '';
        for ($i = 0; $i < 12; $i++) {
            $rfid .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $rfid;
    }
}