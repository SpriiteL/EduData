<?php

namespace App\Entity;

use App\Repository\ImprimanteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImprimanteRepository::class)]
class Imprimante
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $printer = null;

    #[ORM\Column]
    private ?int $nbCopyBW = null;

    #[ORM\Column]
    private ?int $nbCopyColor = null;

    #[ORM\Column(length: 255)]
    private ?string $idPrinter = null;

    #[ORM\Column(length: 255)]
    private ?string $namePrinter = null;

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

    public function getPrinter(): ?string
    {
        return $this->printer;
    }

    public function setPrinter(string $printer): static
    {
        $this->printer = $printer;

        return $this;
    }

    public function getNbCopyBW(): ?int
    {
        return $this->nbCopyBW;
    }

    public function setNbCopyBW(int $nbCopyBW): static
    {
        $this->nbCopyBW = $nbCopyBW;

        return $this;
    }

    public function getNbCopyColor(): ?int
    {
        return $this->nbCopyColor;
    }

    public function setNbCopyColor(int $nbCopyColor): static
    {
        $this->nbCopyColor = $nbCopyColor;

        return $this;
    }

    public function getIdPrinter(): ?string
    {
        return $this->idPrinter;
    }

    public function setIdPrinter(string $idPrinter): static
    {
        $this->idPrinter = $idPrinter;

        return $this;
    }

    public function getNamePrinter(): ?string
    {
        return $this->namePrinter;
    }

    public function setNamePrinter(string $namePrinter): static
    {
        $this->namePrinter = $namePrinter;

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