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

    /**
     * Affiche la liste des stats
     */
    #[Route('/printer-stats', name: 'app_printer_stats', methods: ['GET'])]
    public function index(): Response
    {
        $stats = $this->doctrine->getRepository(PrinterStat::class)->findAll();

        return $this->render('printer_stat/index.html.twig', [
            'stats' => $stats,
        ]);
    }

    /**
     * Retourne les stats au format JSON
     */
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

    /**
     * Import des stats depuis un fichier CSV uploadé
     */
    #[Route('/printer-stats/import', name: 'app_printer_stats_import', methods: ['POST'])]
    public function importCsv(Request $request): JsonResponse
    {
        /** @var UploadedFile|null $file */
        $file = $request->files->get('csvFile');

        if (!$file) {
            return new JsonResponse(['error' => 'Aucun fichier fourni'], 400);
        }

        // Validation simple du type de fichier
        if ($file->getClientMimeType() !== 'text/csv' && $file->getClientOriginalExtension() !== 'csv') {
            return new JsonResponse(['error' => 'Le fichier doit être au format CSV'], 400);
        }

        try {
            $entityManager = $this->doctrine->getManager();

            // Supprimer les anciennes données
            $entityManager->createQuery('DELETE FROM App\Entity\PrinterStat')->execute();

            // Parser et traiter le CSV
            $csvData = $this->parseCsvFile($file);
            if (empty($csvData)) {
                return new JsonResponse(['error' => 'Le fichier CSV est vide ou invalide'], 400);
            }

            $userStats = $this->processUserStats($csvData);

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
                'message' => 'Données importées avec succès',
                'usersProcessed' => count($userStats),
                'usernames' => array_keys($userStats),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors du traitement du fichier: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Lit et extrait les données du CSV
     */
    private function parseCsvFile(UploadedFile $file): array
    {
        $csvData = [];
        $handle = fopen($file->getPathname(), 'r');

        if (!$handle) {
            return [];
        }

        // Ignorer les 4 premières lignes (header spécifique)
        for ($i = 0; $i < 4; $i++) {
            fgets($handle);
        }

        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            return [];
        }

        $headers = array_map('trim', $headers);

        // Recherche des colonnes importantes
        $ownerIndex = array_search('Owner name', $headers);
        $jobKindIndex = array_search('Job kind', $headers);
        $colorIndex = array_search('Color', $headers);
        $originalPagesIndex = array_search('Original pages', $headers);
        $printCountIndex = array_search('Print count', $headers);

        if (in_array(false, [$ownerIndex, $jobKindIndex, $colorIndex, $originalPagesIndex, $printCountIndex], true)) {
            fclose($handle);
            return [];
        }

        // Parcourir les lignes restantes
        while (($row = fgetcsv($handle)) !== false) {
            // Vérification minimale de longueur de ligne
            if (count($row) <= max($ownerIndex, $jobKindIndex, $colorIndex, $originalPagesIndex, $printCountIndex)) {
                continue;
            }

            $owner = strtoupper(trim(preg_replace('/\s+/', ' ', $row[$ownerIndex] ?? '')));
            $jobKind = ucfirst(strtolower(trim($row[$jobKindIndex] ?? '')));
            $color = ucfirst(strtolower(trim($row[$colorIndex] ?? '')));
            $originalPages = is_numeric($row[$originalPagesIndex]) ? (int)$row[$originalPagesIndex] : 0;
            $printCount = is_numeric($row[$printCountIndex]) ? (int)$row[$printCountIndex] : 0;

            // Filtrage des données non valides ou indésirables
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

    /**
     * Traite les données CSV et agrège les statistiques par utilisateur
     */
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

    /**
     * Supprime toutes les données PrinterStat
     */
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
}
