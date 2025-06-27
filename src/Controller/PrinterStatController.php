<?php
// src/Controller/PrinterStatController.php
namespace App\Controller;

use App\Entity\PrinterStat;
use Doctrine\Persistence\ManagerRegistry;
use League\Csv\Writer;
use League\Csv\Reader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

class PrinterStatController extends AbstractController
{
    #[Route('/printer-stats', name: 'app_printer_stats')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $stats = $doctrine->getRepository(PrinterStat::class)->findAll();
        
        return $this->render('printer_stat/index.html.twig', [
            'stats' => $stats,
        ]);
    }

    #[Route('/printer-stats/import', name: 'app_printer_stats_import', methods: ['POST'])]
    public function import(Request $request, ManagerRegistry $doctrine): Response
    {
        $file = $request->files->get('csv');
        
        if ($file && $file->isValid()) {
            try {
                $userStats = $this->processCsvFile($file);
                $entityManager = $doctrine->getManager();
                
                // Supprimer les anciennes données
                $entityManager->createQuery('DELETE FROM App\Entity\PrinterStat')->execute();
                
                // Persister les nouvelles données
                $count = $this->saveImportedData($userStats, $doctrine);
                
                // Si la requête est AJAX, renvoyer les données en JSON
                if ($request->isXmlHttpRequest() || $request->headers->get('Accept') === 'application/json') {
                    $stats = $doctrine->getRepository(PrinterStat::class)->findAll();
                    return $this->json([
                        'stats' => $this->transformStatsToArray($stats),
                        'success' => true,
                        'message' => "$count enregistrement(s) importé(s) avec succès."
                    ]);
                }
                
                $this->addFlash('success', "$count enregistrement(s) importé(s) avec succès.");
            } catch (\Exception $e) {
                if ($request->isXmlHttpRequest() || $request->headers->get('Accept') === 'application/json') {
                    return $this->json([
                        'success' => false,
                        'message' => 'Erreur lors de l\'import : ' . $e->getMessage()
                    ], 400);
                }
                
                $this->addFlash('error', 'Erreur lors de l\'import : ' . $e->getMessage());
            }
        } else {
            if ($request->isXmlHttpRequest() || $request->headers->get('Accept') === 'application/json') {
                return $this->json([
                    'success' => false,
                    'message' => 'Veuillez télécharger un fichier valide.'
                ], 400);
            }
            
            $this->addFlash('error', 'Veuillez télécharger un fichier valide.');
        }
        
        return $this->redirectToRoute('app_printer_stats');
    }

    #[Route('/printer-stats/export', name: 'app_printer_stats_export', methods: ['GET'])]
    public function export(ManagerRegistry $doctrine): Response
    {
        $stats = $doctrine->getRepository(PrinterStat::class)->findAll();
        
        $csvFileName = 'statistiques_imprimantes_' . date('Ymd') . '.csv';
        $outputBuffer = fopen('php://temp', 'r+');
        
        // Ajouter le BOM pour UTF-8
        fwrite($outputBuffer, "\xEF\xBB\xBF");
        
        // Écrire l'en-tête CSV
        fputcsv($outputBuffer, [
            'Utilisateur',
            'Job charge count FCL',
            'Job charge count FCS',
            'Total Impression Couleur',
            'Job charge count MTL',
            'Job charge count MTS',
            'Total Impression Mono',
            'Job charge count MCL',
            'Job charge count MCS',
            'Total Copie Couleur',
            'Job charge count MBL',
            'Job charge count MBS',
            'Total Copie Mono',
            'Total Couleur',
            'Total Noir',
            'Scan A4',
            'Scan A3',
            'Total Scan'
        ], ';');
        
        // Écrire les données
        foreach ($stats as $stat) {
            fputcsv($outputBuffer, [
                $stat->getUsername(),
                $stat->getJobChargeCountFCL(),
                $stat->getJobChargeCountFCS(),
                $stat->getImpressionTotalCouleur(),
                $stat->getJobChargeCountMTL(),
                $stat->getJobChargeCountMTS(),
                $stat->getImpressionTotalMono(),
                $stat->getJobChargeCountMCL(),
                $stat->getJobChargeCountMCS(),
                $stat->getCopieTotalCouleur(),
                $stat->getJobChargeCountMBL(),
                $stat->getJobChargeCountMBS(),
                $stat->getCopieTotalMono(),
                $stat->getTotalCouleur(),
                $stat->getTotalNoir(),
                $stat->getScanA4(),
                $stat->getScanA3(),
                $stat->getTotalScan()
            ], ';');
        }
        
        rewind($outputBuffer);
        
        $response = new Response(stream_get_contents($outputBuffer));
        fclose($outputBuffer);
        
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $csvFileName . '"');
        
        return $response;
    }

    #[Route('/printer-stats/api/stats', name: 'api_printer_stats', methods: ['GET'])]
    public function apiStats(ManagerRegistry $doctrine, SerializerInterface $serializer): Response
    {
        $stats = $doctrine->getRepository(PrinterStat::class)->findAll();
        
        // Sérialisation des données en JSON
        $jsonContent = $serializer->serialize($stats, 'json');
        
        return new Response($jsonContent, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/printer-stats/api/stats/{id}', name: 'api_delete_printer_stat', methods: ['DELETE'])]
    public function apiDeleteStat(int $id, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $stat = $doctrine->getRepository(PrinterStat::class)->find($id);
        
        if ($stat) {
            $entityManager->remove($stat);
            $entityManager->flush();
            return $this->json(['message' => 'Statistique supprimée avec succès'], 200);
        }
        
        return $this->json(['message' => 'Statistique non trouvée'], 404);
    }

    // Méthode pour traiter le fichier CSV
    private function processCsvFile(UploadedFile $file): array
    {
        $handle = fopen($file->getPathname(), 'r');
        
        // Ignorer les lignes d'en-tête (5 premières lignes)
        for ($i = 0; $i < 5; $i++) {
            fgetcsv($handle, 1000, ',');
        }
        
        // Lire la ligne d'en-tête des colonnes de données
        $header = fgetcsv($handle, 1000, ',');
        
        // Tableau pour stocker les statistiques par utilisateur
        $userStats = [];
        
        // Lire les données ligne par ligne
        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            // Ignorer les lignes trop courtes (qui ne sont pas des données)
            if (count($row) < 50) continue;
            
            // Extraire le nom d'utilisateur (Owner name, colonne 3)
            $username = trim($row[3]);
            
            // Ignorer les lignes sans nom d'utilisateur
            if (empty($username)) continue;
            
            if (!isset($userStats[$username])) {
                $userStats[$username] = [
                    'jobChargeCountFCL' => 0,
                    'jobChargeCountFCS' => 0,
                    'impressionTotalCouleur' => 0,
                    'jobChargeCountMTL' => 0,
                    'jobChargeCountMTS' => 0,
                    'impressionTotalMono' => 0,
                    'jobChargeCountMCL' => 0,
                    'jobChargeCountMCS' => 0,
                    'copieTotalCouleur' => 0,
                    'jobChargeCountMBL' => 0,
                    'jobChargeCountMBS' => 0,
                    'copieTotalMono' => 0,
                    'totalCouleur' => 0,
                    'totalNoir' => 0,
                    'scanA4' => 0,
                    'scanA3' => 0,
                    'totalScan' => 0
                ];
            }
            
            // Analyser le type de travail (Print/Copy/Scan) - colonne 0
            $jobType = trim($row[0]);
            // Format du papier (A3 ou A4) - colonne 28 "Output paper size"
            $paperSize = strpos($row[28], 'A3') !== false ? 'A3' : 'A4';
            // Couleur ou Noir - colonne 30 "Color"
            $colorMode = trim($row[30]) === 'Black' ? 'Noir' : 'Couleur';
            // Nombre de copies réalisées - colonne 42 "Print count"
            $printCount = (int)$row[42];
            
            // Si pas de copies, ignorer cette ligne
            if ($printCount <= 0) continue;
            
            // Mettre à jour les statistiques en fonction du type de travail, couleur et format
            if ($jobType === 'Print') {
                if ($colorMode === 'Couleur') {
                    if ($paperSize === 'A4') {
                        $userStats[$username]['jobChargeCountFCL'] += $printCount;
                    } else { // A3
                        $userStats[$username]['jobChargeCountFCS'] += $printCount;
                    }
                    $userStats[$username]['impressionTotalCouleur'] += $printCount;
                    $userStats[$username]['totalCouleur'] += $printCount;
                } else { // Noir
                    if ($paperSize === 'A4') {
                        $userStats[$username]['jobChargeCountMTL'] += $printCount;
                    } else { // A3
                        $userStats[$username]['jobChargeCountMTS'] += $printCount;
                    }
                    $userStats[$username]['impressionTotalMono'] += $printCount;
                    $userStats[$username]['totalNoir'] += $printCount;
                }
            } elseif ($jobType === 'Copy') {
                if ($colorMode === 'Couleur') {
                    if ($paperSize === 'A4') {
                        $userStats[$username]['jobChargeCountMCL'] += $printCount;
                    } else { // A3
                        $userStats[$username]['jobChargeCountMCS'] += $printCount;
                    }
                    $userStats[$username]['copieTotalCouleur'] += $printCount;
                    $userStats[$username]['totalCouleur'] += $printCount;
                } else { // Noir
                    if ($paperSize === 'A4') {
                        $userStats[$username]['jobChargeCountMBL'] += $printCount;
                    } else { // A3
                        $userStats[$username]['jobChargeCountMBS'] += $printCount;
                    }
                    $userStats[$username]['copieTotalMono'] += $printCount;
                    $userStats[$username]['totalNoir'] += $printCount;
                }
            } elseif ($jobType === 'Scan') {
                if ($paperSize === 'A4') {
                    $userStats[$username]['scanA4'] += $printCount;
                } else { // A3
                    $userStats[$username]['scanA3'] += $printCount;
                }
                $userStats[$username]['totalScan'] += $printCount;
            }
        }
        
        fclose($handle);
        return $userStats;
    }

    // Méthode pour enregistrer les données importées dans la base de données
    private function saveImportedData(array $userStats, ManagerRegistry $doctrine): int
    {
        $entityManager = $doctrine->getManager();
        $count = 0;
        
        // Persister les statistiques par utilisateur
        foreach ($userStats as $username => $stats) {
            $stat = new PrinterStat();
            $stat->setUsername($username);
            $stat->setJobChargeCountFCL($stats['jobChargeCountFCL']);
            $stat->setJobChargeCountFCS($stats['jobChargeCountFCS']);
            $stat->setImpressionTotalCouleur($stats['impressionTotalCouleur']);
            $stat->setJobChargeCountMTL($stats['jobChargeCountMTL']);
            $stat->setJobChargeCountMTS($stats['jobChargeCountMTS']);
            $stat->setImpressionTotalMono($stats['impressionTotalMono']);
            $stat->setJobChargeCountMCL($stats['jobChargeCountMCL']);
            $stat->setJobChargeCountMCS($stats['jobChargeCountMCS']);
            $stat->setCopieTotalCouleur($stats['copieTotalCouleur']);
            $stat->setJobChargeCountMBL($stats['jobChargeCountMBL']);
            $stat->setJobChargeCountMBS($stats['jobChargeCountMBS']);
            $stat->setCopieTotalMono($stats['copieTotalMono']);
            $stat->setTotalCouleur($stats['totalCouleur']);
            $stat->setTotalNoir($stats['totalNoir']);
            $stat->setScanA4($stats['scanA4']);
            $stat->setScanA3($stats['scanA3']);
            $stat->setTotalScan($stats['totalScan']);
            
            $entityManager->persist($stat);
            $count++;
        }
        
        $entityManager->flush();
        return $count;
    }

    // Méthode pour transformer les statistiques en tableau
    private function transformStatsToArray(array $stats): array
    {
        $statsArray = [];
        
        foreach ($stats as $stat) {
            $statsArray[] = [
                'id' => $stat->getId(),
                'username' => $stat->getUsername(),
                'jobChargeCountFCL' => $stat->getJobChargeCountFCL(),
                'jobChargeCountFCS' => $stat->getJobChargeCountFCS(),
                'impressionTotalCouleur' => $stat->getImpressionTotalCouleur(),
                'jobChargeCountMTL' => $stat->getJobChargeCountMTL(),
                'jobChargeCountMTS' => $stat->getJobChargeCountMTS(),
                'impressionTotalMono' => $stat->getImpressionTotalMono(),
                'jobChargeCountMCL' => $stat->getJobChargeCountMCL(),
                'jobChargeCountMCS' => $stat->getJobChargeCountMCS(),
                'copieTotalCouleur' => $stat->getCopieTotalCouleur(),
                'jobChargeCountMBL' => $stat->getJobChargeCountMBL(),
                'jobChargeCountMBS' => $stat->getJobChargeCountMBS(),
                'copieTotalMono' => $stat->getCopieTotalMono(),
                'totalCouleur' => $stat->getTotalCouleur(),
                'totalNoir' => $stat->getTotalNoir(),
                'scanA4' => $stat->getScanA4(),
                'scanA3' => $stat->getScanA3(),
                'totalScan' => $stat->getTotalScan(),
            ];
        }
        
        return $statsArray;
    }
}