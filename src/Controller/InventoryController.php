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
                // Lire le contenu du fichier
                $content = file_get_contents($file->getPathname());

                // Convertir le contenu en UTF-8 si nécessaire
                if (mb_detect_encoding($content, 'UTF-8', true) === false) {
                    $content = mb_convert_encoding($content, 'UTF-8', 'ISO-8859-1');
                }

                // Créer le lecteur CSV à partir du contenu
                $csv = Reader::createFromString($content);
                $csv->setDelimiter(';');
                $csv->setHeaderOffset(0);

                $records = $csv->getRecords();

                // Normaliser les en-têtes en UTF-8 et enlever les espaces
                $headers = array_map(fn($header) => trim($header), $csv->getHeader());
                $this->addFlash('info', 'Colonnes trouvées dans le fichier : ' . implode(', ', $headers));

                $entityManager = $doctrine->getManager();

                $requiredColumns = [
                    "Type d'Actif", "Fournisseur", "Date d'arrivée",
                    "Numéro de Série", "Numéro Facture Interne",
                    "Numéro de Facture", "Prix Neuf",
                    "Numero de produit de la série",
                    "Nombre total de produits dans le lot"
                ];

                $count = 0;
                foreach ($records as $row) {
                    // Normaliser les valeurs des lignes
                    $normalizedRow = array_map(fn($value) => trim($value), $row);
                    $missingColumns = [];
                    foreach ($requiredColumns as $column) {
                        if (!isset($normalizedRow[$column])) {
                            $missingColumns[] = $column;
                        }
                    }

                    // Si des colonnes sont manquantes, affiche un message d'erreur spécifique
                    if (!empty($missingColumns)) {
                        $this->addFlash('error', 'Colonnes manquantes pour un enregistrement : ' . implode(', ', $missingColumns));
                        continue;
                    }

                    // Création de l'objet Inventory et ajout des valeurs
                    $inventory = new Inventory();
                    try {
                        $inventory->setActiveType($normalizedRow["Type d'Actif"]);
                        $inventory->setProvider($normalizedRow['Fournisseur']);
                        $inventory->setDateEntry(new \DateTime($normalizedRow["Date d'arrivée"]));
                        $inventory->setNumSerie($normalizedRow['Numéro de Série']);
                        $inventory->setNumInvoiceIntern($normalizedRow['Numéro Facture Interne']);
                        $inventory->setNumInvoice($normalizedRow['Numéro de Facture']);
                        $inventory->setPrice(floatval($normalizedRow['Prix Neuf']));
                        $inventory->setNumProductSerie($normalizedRow['Numero de produit de la série']);
                        $inventory->setTotalProductLot(intval($normalizedRow['Nombre total de produits dans le lot']));

                        $entityManager->persist($inventory);
                        $count++;
                    } catch (\Exception $e) {
                        $this->addFlash('error', 'Erreur lors de l\'ajout de l\'enregistrement : ' . $e->getMessage());
                    }
                }

                if ($count > 0) {
                    $entityManager->flush();
                    $this->addFlash('success', $count . ' enregistrement(s) importé(s) avec succès.');
                } else {
                    $this->addFlash('error', 'Aucun enregistrement valide trouvé dans le fichier.');
                }
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
