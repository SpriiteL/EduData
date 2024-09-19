<?php

namespace App\Entity;

use App\Repository\InventoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InventoryRepository::class)]
class Inventory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $activeType = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column]
    private ?int $numProductSerie = null;

    #[ORM\Column]
    private ?int $totalProductLot = null;

    #[ORM\Column(length: 255)]
    private ?string $provider = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateEntry = null;

    #[ORM\Column(length: 255)]
    private ?string $numSerie = null;

    #[ORM\Column(length: 255)]
    private ?string $numInvoiceIntern = null;

    #[ORM\Column(length: 255)]
    private ?string $numInvoice = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActiveType(): ?string
    {
        return $this->activeType;
    }

    public function setActiveType(string $activeType): static
    {
        $this->activeType = $activeType;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getNumProductSerie(): ?int
    {
        return $this->numProductSerie;
    }

    public function setNumProductSerie(int $numProductSerie): static
    {
        $this->numProductSerie = $numProductSerie;

        return $this;
    }

    public function getTotalProductLot(): ?int
    {
        return $this->totalProductLot;
    }

    public function setTotalProductLot(int $totalProductLot): static
    {
        $this->totalProductLot = $totalProductLot;

        return $this;
    }

    public function getProvider(): ?string
    {
        return $this->provider;
    }

    public function setProvider(string $provider): static
    {
        $this->provider = $provider;

        return $this;
    }

    public function getDateEntry(): ?\DateTimeInterface
    {
        return $this->dateEntry;
    }

    public function setDateEntry(\DateTimeInterface $dateEntry): static
    {
        $this->dateEntry = $dateEntry;

        return $this;
    }

    public function getNumSerie(): ?string
    {
        return $this->numSerie;
    }

    public function setNumSerie(string $numSerie): static
    {
        $this->numSerie = $numSerie;

        return $this;
    }

    public function getNumInvoiceIntern(): ?string
    {
        return $this->numInvoiceIntern;
    }

    public function setNumInvoiceIntern(string $numInvoiceIntern): static
    {
        $this->numInvoiceIntern = $numInvoiceIntern;

        return $this;
    }

    public function getNumInvoice(): ?string
    {
        return $this->numInvoice;
    }

    public function setNumInvoice(string $numInvoice): static
    {
        $this->numInvoice = $numInvoice;

        return $this;
    }
}
