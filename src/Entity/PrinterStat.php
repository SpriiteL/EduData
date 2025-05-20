<?php

namespace App\Entity;

use App\Repository\PrinterStatRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrinterStatRepository::class)]
class PrinterStat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column]
    private ?int $jobChargeCountFCL = 0;

    #[ORM\Column]
    private ?int $jobChargeCountFCS = 0;

    #[ORM\Column]
    private ?int $impressionTotalCouleur = 0;

    #[ORM\Column]
    private ?int $jobChargeCountMTL = 0;

    #[ORM\Column]
    private ?int $jobChargeCountMTS = 0;

    #[ORM\Column]
    private ?int $impressionTotalMono = 0;

    #[ORM\Column]
    private ?int $jobChargeCountMCL = 0;

    #[ORM\Column]
    private ?int $jobChargeCountMCS = 0;

    #[ORM\Column]
    private ?int $copieTotalCouleur = 0;

    #[ORM\Column]
    private ?int $jobChargeCountMBL = 0;

    #[ORM\Column]
    private ?int $jobChargeCountMBS = 0;

    #[ORM\Column]
    private ?int $copieTotalMono = 0;

    #[ORM\Column]
    private ?int $totalCouleur = 0;

    #[ORM\Column]
    private ?int $totalNoir = 0;

    #[ORM\Column]
    private ?int $scanA4 = 0;

    #[ORM\Column]
    private ?int $scanA3 = 0;

    #[ORM\Column]
    private ?int $totalScan = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getJobChargeCountFCL(): ?int
    {
        return $this->jobChargeCountFCL;
    }

    public function setJobChargeCountFCL(int $jobChargeCountFCL): static
    {
        $this->jobChargeCountFCL = $jobChargeCountFCL;

        return $this;
    }

    public function getJobChargeCountFCS(): ?int
    {
        return $this->jobChargeCountFCS;
    }

    public function setJobChargeCountFCS(int $jobChargeCountFCS): static
    {
        $this->jobChargeCountFCS = $jobChargeCountFCS;

        return $this;
    }

    public function getImpressionTotalCouleur(): ?int
    {
        return $this->impressionTotalCouleur;
    }

    public function setImpressionTotalCouleur(int $impressionTotalCouleur): static
    {
        $this->impressionTotalCouleur = $impressionTotalCouleur;

        return $this;
    }

    public function getJobChargeCountMTL(): ?int
    {
        return $this->jobChargeCountMTL;
    }

    public function setJobChargeCountMTL(int $jobChargeCountMTL): static
    {
        $this->jobChargeCountMTL = $jobChargeCountMTL;

        return $this;
    }

    public function getJobChargeCountMTS(): ?int
    {
        return $this->jobChargeCountMTS;
    }

    public function setJobChargeCountMTS(int $jobChargeCountMTS): static
    {
        $this->jobChargeCountMTS = $jobChargeCountMTS;

        return $this;
    }

    public function getImpressionTotalMono(): ?int
    {
        return $this->impressionTotalMono;
    }

    public function setImpressionTotalMono(int $impressionTotalMono): static
    {
        $this->impressionTotalMono = $impressionTotalMono;

        return $this;
    }

    public function getJobChargeCountMCL(): ?int
    {
        return $this->jobChargeCountMCL;
    }

    public function setJobChargeCountMCL(int $jobChargeCountMCL): static
    {
        $this->jobChargeCountMCL = $jobChargeCountMCL;

        return $this;
    }

    public function getJobChargeCountMCS(): ?int
    {
        return $this->jobChargeCountMCS;
    }

    public function setJobChargeCountMCS(int $jobChargeCountMCS): static
    {
        $this->jobChargeCountMCS = $jobChargeCountMCS;

        return $this;
    }

    public function getCopieTotalCouleur(): ?int
    {
        return $this->copieTotalCouleur;
    }

    public function setCopieTotalCouleur(int $copieTotalCouleur): static
    {
        $this->copieTotalCouleur = $copieTotalCouleur;

        return $this;
    }

    public function getJobChargeCountMBL(): ?int
    {
        return $this->jobChargeCountMBL;
    }

    public function setJobChargeCountMBL(int $jobChargeCountMBL): static
    {
        $this->jobChargeCountMBL = $jobChargeCountMBL;

        return $this;
    }

    public function getJobChargeCountMBS(): ?int
    {
        return $this->jobChargeCountMBS;
    }

    public function setJobChargeCountMBS(int $jobChargeCountMBS): static
    {
        $this->jobChargeCountMBS = $jobChargeCountMBS;

        return $this;
    }

    public function getCopieTotalMono(): ?int
    {
        return $this->copieTotalMono;
    }

    public function setCopieTotalMono(int $copieTotalMono): static
    {
        $this->copieTotalMono = $copieTotalMono;

        return $this;
    }

    public function getTotalCouleur(): ?int
    {
        return $this->totalCouleur;
    }

    public function setTotalCouleur(int $totalCouleur): static
    {
        $this->totalCouleur = $totalCouleur;

        return $this;
    }

    public function getTotalNoir(): ?int
    {
        return $this->totalNoir;
    }

    public function setTotalNoir(int $totalNoir): static
    {
        $this->totalNoir = $totalNoir;

        return $this;
    }

    public function getScanA4(): ?int
    {
        return $this->scanA4;
    }

    public function setScanA4(int $scanA4): static
    {
        $this->scanA4 = $scanA4;

        return $this;
    }

    public function getScanA3(): ?int
    {
        return $this->scanA3;
    }

    public function setScanA3(int $scanA3): static
    {
        $this->scanA3 = $scanA3;

        return $this;
    }

    public function getTotalScan(): ?int
    {
        return $this->totalScan;
    }

    public function setTotalScan(int $totalScan): static
    {
        $this->totalScan = $totalScan;

        return $this;
    }
}