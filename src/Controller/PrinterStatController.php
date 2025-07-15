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
    public function getData(): JsonResponse
    {
        $stats = $this->doctrine->getRepository(PrinterStat::class)->findAll();

        $data = [];
        foreach ($stats as $stat) {
            $data[] = [
                'id' => $stat->getId(),
                'username' => $stat->getUsername(),
                'totalBlack' => $stat->getTotalBlack(),
                'totalColor' => $stat->getTotalColor(),
                'totalScans' => $stat->getTotalScans(),
                'createdAt' => $stat->getCreatedAt()?->format('Y-m-d H:i:s'),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/printer-stats/import', name: 'app_printer_stats_import', methods: ['POST'])]
    public function importCsv(Request $request): JsonResponse
    {
        $files = $request->files->all('csvFiles');

        if (empty($files)) {
            return new JsonResponse(['error' => 'Aucun fichier fourni'], 400);
        }

        foreach ($files as $file) {
            if (
                !$file instanceof UploadedFile ||
                ($file->getClientMimeType() !== 'text/csv' && $file->getClientOriginalExtension() !== 'csv')
            ) {
                return new JsonResponse(['error' => 'Tous les fichiers doivent être au format CSV'], 400);
            }
        }

        try {
            $entityManager = $this->doctrine->getManager();
            $entityManager->createQuery('DELETE FROM App\Entity\PrinterStat')->execute();

            $allCsvData = [];

            foreach ($files as $file) {
                $parsedData = $this->parseCsvFile($file);
                if (!empty($parsedData)) {
                    $allCsvData = array_merge($allCsvData, $parsedData);
                }
            }

            if (empty($allCsvData)) {
                return new JsonResponse(['error' => 'Aucun fichier CSV valide ou vide'], 400);
            }

            $userStats = $this->processUserStats($allCsvData);

            foreach ($userStats as $username => $stats) {
                $printerStat = new PrinterStat();
                $printerStat->setUsername($username);
                $printerStat->setTotalBlack($stats['totalBlack']);
                $printerStat->setTotalColor($stats['totalColor']);
                $printerStat->setTotalScans($stats['totalScans']);
                $printerStat->setCreatedAt(new \DateTime());

                $entityManager->persist($printerStat);
            }

            $entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Import réussi',
                'usersProcessed' => count($userStats),
                'usernames' => array_keys($userStats),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors du traitement des fichiers : ' . $e->getMessage()], 500);
        }
    }

    private function parseCsvFile(UploadedFile $file): array
    {
        $csvData = [];
        $handle = fopen($file->getPathname(), 'r');

        if (!$handle) {
            return [];
        }

        for ($i = 0; $i < 4; $i++) {
            fgets($handle);
        }

        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            return [];
        }

        $headers = array_map('trim', $headers);

        $ownerIndex = array_search('Owner name', $headers);
        $jobKindIndex = array_search('Job kind', $headers);
        $colorIndex = array_search('Color', $headers);
        $originalPagesIndex = array_search('Original pages', $headers);
        $printCountIndex = array_search('Print count', $headers);

        if (in_array(false, [$ownerIndex, $jobKindIndex, $colorIndex, $originalPagesIndex, $printCountIndex], true)) {
            fclose($handle);
            return [];
        }

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) <= max($ownerIndex, $jobKindIndex, $colorIndex, $originalPagesIndex, $printCountIndex)) {
                continue;
            }

            $owner = strtoupper(trim(preg_replace('/\s+/', ' ', $row[$ownerIndex] ?? '')));
            $jobKind = ucfirst(strtolower(trim($row[$jobKindIndex] ?? '')));
            $color = ucfirst(strtolower(trim($row[$colorIndex] ?? '')));
            $originalPages = is_numeric($row[$originalPagesIndex]) ? (int)$row[$originalPagesIndex] : 0;
            $printCount = is_numeric($row[$printCountIndex]) ? (int)$row[$printCountIndex] : 0;

            if (
                empty($owner) ||
                in_array($owner, ['COPY', 'PRINT', 'SCAN', 'JOB KIND', 'DOCUMENT'], true) ||
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
            ];
        }

        fclose($handle);

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
    public function clearData(): JsonResponse
    {
        try {
            $entityManager = $this->doctrine->getManager();
            $entityManager->createQuery('DELETE FROM App\Entity\PrinterStat')->execute();

            return new JsonResponse(['success' => true, 'message' => 'Données supprimées avec succès']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la suppression: ' . $e->getMessage()], 500);
        }
    }

    #[Route('/printer-stats/export', name: 'app_printer_stats_export', methods: ['GET'])]
    public function exportCsv(): Response
    {
        $stats = $this->doctrine->getRepository(PrinterStat::class)->findAll();

        $handle = fopen('php://temp', 'r+');

        // BOM UTF-8 pour Excel
        fwrite($handle, "\xEF\xBB\xBF");

        // Entêtes avec ;
        fputcsv($handle, ['Username', 'Total Noir', 'Total Couleur', 'Total Scans', 'Date de création'], ';');

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
                $stat->getCreatedAt()?->format('Y-m-d H:i:s'),
            ], ';');
        }

        // Ligne TOTAL
        fputcsv($handle, [
            'TOTAL GÉNÉRAL',
            $totalBlack,
            $totalColor,
            $totalScans,
            '',
        ], ';');

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return new Response($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="printer_stats_export.csv"',
        ]);
    }
}
