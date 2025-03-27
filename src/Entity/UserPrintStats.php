<?php

namespace App\Entity;

use App\Repository\UserPrintStatsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserPrintStatsRepository::class)]
class UserPrintStats
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $ownerName = null;

    #[ORM\Column]
    private ?int $impressionCouleurA4 = null;

    #[ORM\Column]
    private ?int $impressionCouleurSupA4 = null;

    #[ORM\Column]
    private ?int $impressionTotalCouleur = null;

    #[ORM\Column]
    private ?int $impressionMonoA4 = null;

    #[ORM\Column]
    private ?int $impressionMonoSupA4 = null;

    #[ORM\Column]
    private ?int $impressionTotalMono = null;

    #[ORM\Column]
    private ?int $copieCouleurA4 = null;

    #[ORM\Column]
    private ?int $copieCouleurSupA4 = null;

    #[ORM\Column]
    private ?int $copieTotalCouleur = null;

    #[ORM\Column]
    private ?int $copieMonoA4 = null;

    #[ORM\Column]
    private ?int $copieMonoSupA4 = null;

    #[ORM\Column]
    private ?int $copieTotalMono = null;

    #[ORM\Column]
    private ?int $totalCouleur = null;

    #[ORM\Column]
    private ?int $totalNoir = null;

    #[ORM\Column]
    private ?int $scanA4 = null;

    #[ORM\Column]
    private ?int $scanA3 = null;

    #[ORM\Column]
    private ?int $totalScans = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getOwnerName(): ?string
    {
        return $this->ownerName;
    }

    public function setOwnerName(string $ownerName): static
    {
        $this->ownerName = $ownerName;

        return $this;
    }

    public function getImpressionCouleurA4(): ?int
    {
        return $this->impressionCouleurA4;
    }

    public function setImpressionCouleurA4(int $impressionCouleurA4): static
    {
        $this->impressionCouleurA4 = $impressionCouleurA4;

        return $this;
    }

    public function getImpressionCouleurSupA4(): ?int
    {
        return $this->impressionCouleurSupA4;
    }

    public function setImpressionCouleurSupA4(int $impressionCouleurSupA4): static
    {
        $this->impressionCouleurSupA4 = $impressionCouleurSupA4;

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

    public function getImpressionMonoA4(): ?int
    {
        return $this->impressionMonoA4;
    }

    public function setImpressionMonoA4(int $impressionMonoA4): static
    {
        $this->impressionMonoA4 = $impressionMonoA4;

        return $this;
    }

    public function getImpressionMonoSupA4(): ?int
    {
        return $this->impressionMonoSupA4;
    }

    public function setImpressionMonoSupA4(int $impressionMonoSupA4): static
    {
        $this->impressionMonoSupA4 = $impressionMonoSupA4;

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

    public function getCopieCouleurA4(): ?int
    {
        return $this->copieCouleurA4;
    }

    public function setCopieCouleurA4(int $copieCouleurA4): static
    {
        $this->copieCouleurA4 = $copieCouleurA4;

        return $this;
    }

    public function getCopieCouleurSupA4(): ?int
    {
        return $this->copieCouleurSupA4;
    }

    public function setCopieCouleurSupA4(int $copieCouleurSupA4): static
    {
        $this->copieCouleurSupA4 = $copieCouleurSupA4;

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

    public function getCopieMonoA4(): ?int
    {
        return $this->copieMonoA4;
    }

    public function setCopieMonoA4(int $copieMonoA4): static
    {
        $this->copieMonoA4 = $copieMonoA4;

        return $this;
    }

    public function getCopieMonoSupA4(): ?int
    {
        return $this->copieMonoSupA4;
    }

    public function setCopieMonoSupA4(int $copieMonoSupA4): static
    {
        $this->copieMonoSupA4 = $copieMonoSupA4;

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

    public function getTotalScans(): ?int
    {
        return $this->totalScans;
    }

    public function setTotalScans(int $totalScans): static
    {
        $this->totalScans = $totalScans;

        return $this;
    }
}
