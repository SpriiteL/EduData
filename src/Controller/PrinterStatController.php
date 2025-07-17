<?php

namespace App\Controller;

use App\Entity\PrinterStat;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PrinterStatController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/printer-stats', name: 'app_printer_stats', methods: ['GET'])]
    public function index(): Response
    {
        $stats = $this->doctrine->getRepository(PrinterStat::class)->findAll();

        return $this->render('printer_stat/index.html.twig', [
            'stats' => $stats,
        ]);
    }

    #[Route('/printer-stats/data', name: 'app_printer_stats_data', methods: ['GET'])]
    public function getData(Request $request): JsonResponse
    {
        $repository = $this->doctrine->getRepository(PrinterStat::class);
        
        // Filtrage par mois si spécifié
        $selectedMonth = $request->query->get('month');
        
        if ($selectedMonth && $selectedMonth !== 'all') {
            $stats = $repository->findBy(['month' => $selectedMonth]);
        } else {
            $stats = $repository->findAll();
        }

        $data = [];
        foreach ($stats as $stat) {
            $data[] = [
                'id' => $stat->getId(),
                'username' => $stat->getUsername(),
                'totalBlack' => $stat->getTotalBlack(),
                'totalColor' => $stat->getTotalColor(),
                'totalScans' => $stat->getTotalScans(),
                'month' => $stat->getMonth(),
                'createdAt' => $stat->getCreatedAt()?->format('Y-m-d H:i:s'),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/printer-stats/import', name: 'app_printer_stats_import', methods: ['POST'])]
    public function importCsv(Request $request): JsonResponse
    {
        try {
            // Récupération des fichiers avec gestion d'erreur
            $files = $request->files->get('csvFiles');
            $selectedMonth = $request->request->get('selectedMonth');

            // Validation des paramètres
            if (!$files || (is_array($files) && empty($files))) {
                return new JsonResponse(['error' => 'Aucun fichier fourni'], 400);
            }

            if (!$selectedMonth) {
                return new JsonResponse(['error' => 'Veuillez sélectionner un mois'], 400);
            }

            // Convertir en tableau si ce n'est pas déjà le cas
            if (!is_array($files)) {
                $files = [$files];
            }

            // Validation des fichiers
            foreach ($files as $file) {
                if (!$file instanceof UploadedFile) {
                    return new JsonResponse(['error' => 'Fichier invalide'], 400);
                }
                
                if ($file->getError() !== UPLOAD_ERR_OK) {
                    return new JsonResponse(['error' => 'Erreur lors du téléchargement du fichier: ' . $file->getError()], 400);
                }

                $mimeType = $file->getMimeType();
                $extension = $file->getClientOriginalExtension();
                
                // Validation plus souple du type MIME
                if (!in_array($mimeType, ['text/csv', 'text/plain', 'application/csv']) && 
                    strtolower($extension) !== 'csv') {
                    return new JsonResponse(['error' => 'Le fichier "' . $file->getClientOriginalName() . '" doit être au format CSV'], 400);
                }

                // Vérifier que le fichier existe et est lisible
                if (!file_exists($file->getPathname()) || !is_readable($file->getPathname())) {
                    return new JsonResponse(['error' => 'Impossible de lire le fichier: ' . $file->getClientOriginalName()], 400);
                }
            }

            $entityManager = $this->doctrine->getManager();
            
            // Supprimer seulement les données du mois sélectionné
            $deletedCount = $entityManager->createQuery('DELETE FROM App\Entity\PrinterStat p WHERE p.month = :month')
                         ->setParameter('month', $selectedMonth)
                         ->execute();

            $allCsvData = [];
            $processedFiles = [];

            foreach ($files as $file) {
                $parsedData = $this->parseCsvFile($file);
                if (!empty($parsedData)) {
                    $allCsvData = array_merge($allCsvData, $parsedData);
                    $processedFiles[] = $file->getClientOriginalName();
                }
            }

            if (empty($allCsvData)) {
                return new JsonResponse(['error' => 'Aucune donnée valide trouvée dans les fichiers CSV'], 400);
            }

            $userStats = $this->processUserStats($allCsvData);

            if (empty($userStats)) {
                return new JsonResponse(['error' => 'Aucune statistique d\'utilisateur générée'], 400);
            }

            $savedCount = 0;
            foreach ($userStats as $username => $stats) {
                $printerStat = new PrinterStat();
                $printerStat->setUsername($username);
                $printerStat->setTotalBlack($stats['totalBlack']);
                $printerStat->setTotalColor($stats['totalColor']);
                $printerStat->setTotalScans($stats['totalScans']);
                $printerStat->setMonth($selectedMonth);
                $printerStat->setCreatedAt(new \DateTime());

                $entityManager->persist($printerStat);
                $savedCount++;
            }

            $entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => "Import réussi pour le mois de " . $this->getMonthName($selectedMonth),
                'usersProcessed' => $savedCount,
                'filesProcessed' => $processedFiles,
                'deletedRecords' => $deletedCount,
                'month' => $selectedMonth,
                'usernames' => array_keys($userStats),
            ]);

        } catch (\Exception $e) {
            // Log l'erreur pour debugging
            error_log('Erreur import CSV: ' . $e->getMessage() . ' - File: ' . $e->getFile() . ' - Line: ' . $e->getLine());
            
            return new JsonResponse([
                'error' => 'Erreur lors du traitement des fichiers: ' . $e->getMessage()
            ], 500);
        }
    }

    private function parseCsvFile(UploadedFile $file): array
    {
        $csvData = [];
        $handle = fopen($file->getPathname(), 'r');

        if (!$handle) {
            throw new \Exception('Impossible d\'ouvrir le fichier: ' . $file->getClientOriginalName());
        }

        try {
            // Ignorer les 4 premières lignes
            for ($i = 0; $i < 4; $i++) {
                $line = fgets($handle);
                if ($line === false) {
                    throw new \Exception('Fichier trop court, impossible de lire les en-têtes');
                }
            }

            // Lire les en-têtes
            $headers = fgetcsv($handle);
            if (!$headers || empty($headers)) {
                throw new \Exception('Impossible de lire les en-têtes du fichier CSV');
            }

            // Nettoyer les en-têtes
            $headers = array_map(function($header) {
                return trim($header);
            }, $headers);

            // Rechercher les index des colonnes importantes
            $ownerIndex = array_search('Owner name', $headers);
            $jobKindIndex = array_search('Job kind', $headers);
            $colorIndex = array_search('Color', $headers);
            $originalPagesIndex = array_search('Original pages', $headers);
            $printCountIndex = array_search('Print count', $headers);

            // Vérifier que toutes les colonnes nécessaires existent
            $missingColumns = [];
            if ($ownerIndex === false) $missingColumns[] = 'Owner name';
            if ($jobKindIndex === false) $missingColumns[] = 'Job kind';
            if ($colorIndex === false) $missingColumns[] = 'Color';
            if ($originalPagesIndex === false) $missingColumns[] = 'Original pages';
            if ($printCountIndex === false) $missingColumns[] = 'Print count';

            if (!empty($missingColumns)) {
                throw new \Exception('Colonnes manquantes dans le fichier CSV: ' . implode(', ', $missingColumns));
            }

            $lineNumber = 5; // Commencer à 5 car on a sauté 4 lignes + en-têtes
            while (($row = fgetcsv($handle)) !== false) {
                $lineNumber++;
                
                // Vérifier que la ligne a assez de colonnes
                if (count($row) <= max($ownerIndex, $jobKindIndex, $colorIndex, $originalPagesIndex, $printCountIndex)) {
                    continue;
                }

                // Extraire et nettoyer les données
                $owner = isset($row[$ownerIndex]) ? strtoupper(trim(preg_replace('/\s+/', ' ', $row[$ownerIndex]))) : '';
                $jobKind = isset($row[$jobKindIndex]) ? ucfirst(strtolower(trim($row[$jobKindIndex]))) : '';
                $color = isset($row[$colorIndex]) ? ucfirst(strtolower(trim($row[$colorIndex]))) : '';
                $originalPages = isset($row[$originalPagesIndex]) && is_numeric($row[$originalPagesIndex]) ? (int)$row[$originalPagesIndex] : 0;
                $printCount = isset($row[$printCountIndex]) && is_numeric($row[$printCountIndex]) ? (int)$row[$printCountIndex] : 0;

                // Filtrer les données invalides
                if (
                    empty($owner) ||
                    in_array($owner, ['COPY', 'PRINT', 'SCAN', 'JOB KIND', 'DOCUMENT', 'OWNER NAME'], true) ||
                    !in_array($jobKind, ['Copy', 'Print', 'Scan'], true) ||
                    ($jobKind === 'Scan' && $originalPages === 0) ||
                    ($jobKind !== 'Scan' && $printCount === 0)
                ) {
                    continue;
                }

                $csvData[] = [
                    'owner' => $owner,
                    'jobKind' => $jobKind,
                    'color' => $color,
                    'originalPages' => $originalPages,
                    'printCount' => $printCount,
                    'lineNumber' => $lineNumber
                ];
            }

        } finally {
            fclose($handle);
        }

        return $csvData;
    }

    private function processUserStats(array $csvData): array
    {
        $userStats = [];

        foreach ($csvData as $row) {
            $owner = $row['owner'];
            $jobKind = $row['jobKind'];
            $color = $row['color'];
            $printCount = $row['printCount'];
            $originalPages = $row['originalPages'];

            if (!isset($userStats[$owner])) {
                $userStats[$owner] = [
                    'totalBlack' => 0,
                    'totalColor' => 0,
                    'totalScans' => 0,
                ];
            }

            if ($jobKind === 'Scan') {
                $userStats[$owner]['totalScans'] += $originalPages;
            } elseif (in_array($jobKind, ['Copy', 'Print'])) {
                if ($color === 'Black') {
                    $userStats[$owner]['totalBlack'] += $printCount;
                } else {
                    $userStats[$owner]['totalColor'] += $printCount;
                }
            }
        }

        return $userStats;
    }

    #[Route('/printer-stats/clear', name: 'app_printer_stats_clear', methods: ['DELETE'])]
    public function clearData(Request $request): JsonResponse
    {
        try {
            $entityManager = $this->doctrine->getManager();
            $month = $request->query->get('month');
            
            if ($month && $month !== 'all') {
                $deletedCount = $entityManager->createQuery('DELETE FROM App\Entity\PrinterStat p WHERE p.month = :month')
                             ->setParameter('month', $month)
                             ->execute();
                $message = "Données du mois de " . $this->getMonthName($month) . " supprimées avec succès ($deletedCount enregistrements)";
            } else {
                $deletedCount = $entityManager->createQuery('DELETE FROM App\Entity\PrinterStat')->execute();
                $message = "Toutes les données supprimées avec succès ($deletedCount enregistrements)";
            }

            return new JsonResponse(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la suppression: ' . $e->getMessage()], 500);
        }
    }

    #[Route('/printer-stats/export', name: 'app_printer_stats_export', methods: ['GET'])]
    public function exportCsv(Request $request): Response
    {
        $repository = $this->doctrine->getRepository(PrinterStat::class);
        $selectedMonth = $request->query->get('month');
        
        if ($selectedMonth && $selectedMonth !== 'all') {
            $stats = $repository->findBy(['month' => $selectedMonth]);
            $filename = "printer_stats_export_$selectedMonth.csv";
        } else {
            $stats = $repository->findAll();
            $filename = "printer_stats_export_all.csv";
        }

        $handle = fopen('php://temp', 'r+');

        // BOM UTF-8 pour Excel
        fwrite($handle, "\xEF\xBB\xBF");

        // Entêtes avec mois
        fputcsv($handle, ['Username', 'Total Noir', 'Total Couleur', 'Total Scans', 'Mois', 'Date de création'], ';');

        $totalBlack = 0;
        $totalColor = 0;
        $totalScans = 0;

        foreach ($stats as $stat) {
            $totalBlack += $stat->getTotalBlack();
            $totalColor += $stat->getTotalColor();
            $totalScans += $stat->getTotalScans();

            fputcsv($handle, [
                $stat->getUsername(),
                $stat->getTotalBlack(),
                $stat->getTotalColor(),
                $stat->getTotalScans(),
                $this->getMonthName($stat->getMonth()),
                $stat->getCreatedAt()?->format('Y-m-d H:i:s'),
            ], ';');
        }

        // Ligne TOTAL
        fputcsv($handle, [
            'TOTAL GÉNÉRAL',
            $totalBlack,
            $totalColor,
            $totalScans,
            $selectedMonth ? $this->getMonthName($selectedMonth) : 'TOUS',
            '',
        ], ';');

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return new Response($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    #[Route('/printer-stats/months', name: 'app_printer_stats_months', methods: ['GET'])]
    public function getAvailableMonths(): JsonResponse
    {
        $repository = $this->doctrine->getRepository(PrinterStat::class);
        $months = $repository->createQueryBuilder('p')
            ->select('DISTINCT p.month')
            ->orderBy('p.month', 'ASC')
            ->getQuery()
            ->getResult();

        $monthsList = array_map(fn($m) => $m['month'], $months);
        
        return new JsonResponse($monthsList);
    }

    private function getMonthName(string $monthValue): string
    {
        $months = [
            '01' => 'Janvier', '02' => 'Février', '03' => 'Mars', '04' => 'Avril',
            '05' => 'Mai', '06' => 'Juin', '07' => 'Juillet', '08' => 'Août',
            '09' => 'Septembre', '10' => 'Octobre', '11' => 'Novembre', '12' => 'Décembre'
        ];
        
        return $months[$monthValue] ?? $monthValue;
    }
}