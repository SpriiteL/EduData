<?php
// src/Controller/InventoryController.php
namespace App\Controller;

use App\Entity\Inventory;
use Doctrine\Persistence\ManagerRegistry;
use League\Csv\Writer;
use League\Csv\Reader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\SerializerInterface;

class InventoryController extends AbstractController
{
    #[Route('/inventory', name: 'app_inventory')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        $etablishment = $user->getEtablishment(); // Récupération de l'établissement de l'utilisateur

        // Recherche des inventaires associés à l'établissement de l'utilisateur
        $inventories = $doctrine->getRepository(Inventory::class)->findBy(['etablishment' => $etablishment]);

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

                        $inventory->setEtablishment($this->getUser()->getEtablishment());

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
        
        // Récupérer l'établissement de l'utilisateur
        $user = $this->getUser();
        $etablishment = $user->getEtablishment(); 
        $inventories = $doctrine->getRepository(Inventory::class)->findBy(['etablishment' => $etablishment]);

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
            "Nombre de Produits", 
            "Nombre Total de Produits",
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

    #[Route('/inventory/api/inventories', name: 'api_inventories', methods: ['GET'])]
    public function apiInventories(ManagerRegistry $doctrine, SerializerInterface $serializer): Response
    {
        $user = $this->getUser();
        $etablishment = $user->getEtablishment(); // Récupération de l'établissement de l'utilisateur

        $inventories = $doctrine->getRepository(Inventory::class)->findBy(['etablishment' => $etablishment]);

        // Sérialisation des données en JSON
        $jsonContent = $serializer->serialize($inventories, 'json');

        return new Response($jsonContent, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/inventory/api/inventories/{id}', name: 'api_delete_inventory', methods: ['DELETE'])]
    public function apiDeleteInventory(int $id, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $inventory = $doctrine->getRepository(Inventory::class)->find($id);

        if ($inventory) {
            $entityManager->remove($inventory);
            $entityManager->flush();
            return $this->json(['message' => 'Inventaire supprimé avec succès'], 200);
        }

        return $this->json(['message' => 'Inventaire non trouvé'], 404);
    }

    // Méthode pour traiter le fichier CSV
    private function processCsvFile(UploadedFile $file)
    {
        // Logic to process CSV file, parse and return data
        $data = [];
        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            while (($row = fgetcsv($handle)) !== false) {
                // Logique pour traiter chaque ligne du CSV
                $data[] = $row;
            }
            fclose($handle);
        }

        return $data;
    }

    // Méthode pour enregistrer les données importées dans la base de données
    private function saveImportedData(array $data, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();

        foreach ($data as $row) {
            $inventory = new Inventory();
            // Assigner les données du CSV à l'inventaire, par exemple :
            // $inventory->setActiveType($row[0]);
            // $inventory->setProvider($row[1]);
            // etc.

            $entityManager->persist($inventory);
        }

        $entityManager->flush();
    }

    // Méthode pour générer un fichier CSV à partir des inventaires
    private function generateCsv(array $inventories)
    {
        $filename = tempnam(sys_get_temp_dir(), 'csv_');
        $file = fopen($filename, 'w');

        // Ajouter les en-têtes du CSV
        fputcsv($file, ['Type Actif', 'Fournisseur', 'Date d\'arrivée', 'Numéro de Série', 'Numéro de Facture', 'Numéro Facture Interne', 'Prix Neuf', 'Numéro de produit de la série', 'Nombre total de produits dans le lot', 'Nom de la Salle']);

        // Ajouter les données des inventaires dans le CSV
        foreach ($inventories as $inventory) {
            fputcsv($file, [
                $inventory->getActiveType(),
                $inventory->getProvider(),
                $inventory->getDateEntry()->format('Y-m-d'),
                $inventory->getNumSerie(),
                $inventory->getNumInvoice(),
                $inventory->getNumInvoiceIntern(),
                $inventory->getPrice(),
                $inventory->getNumProductSerie(),
                $inventory->getTotalProductLot(),
                $inventory->getNameRoom()
            ]);
        }

        fclose($file);
        return $filename;
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
