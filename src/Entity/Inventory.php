<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\InventoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InventoryRepository::class)]
#[ApiResource]
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

    #[ORM\Column(type: 'datetime')]
    private $dateEntry;

    #[ORM\Column(length: 255)]
    private ?string $numSerie = null;

    #[ORM\Column(length: 255)]
    private ?string $numInvoiceIntern = null;

    #[ORM\Column(length: 255)]
    private ?string $numInvoice = null;

    /**
     * @var Collection<int, ExportLogs>
     */
    #[ORM\OneToMany(targetEntity: ExportLogs::class, mappedBy: 'inventory')]
    private Collection $exportLogs;

    /**
     * @var Collection<int, ImportLogs>
     */
    #[ORM\OneToMany(targetEntity: ImportLogs::class, mappedBy: 'inventory')]
    private Collection $importLogs;

    #[ORM\ManyToOne(inversedBy: 'inventories')]
    private ?Etablishment $etablishment = null;


    // #[ORM\Column(length: 255)]
    // private ?string $name = null;

    public function __construct()
    {
        $this->exportLogs = new ArrayCollection();
        $this->importLogs = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, ExportLogs>
     */
    public function getExportLogs(): Collection
    {
        return $this->exportLogs;
    }

    public function addExportLog(ExportLogs $exportLog): static
    {
        if (!$this->exportLogs->contains($exportLog)) {
            $this->exportLogs->add($exportLog);
            $exportLog->setInventory($this);
        }

        return $this;
    }

    public function removeExportLog(ExportLogs $exportLog): static
    {
        if ($this->exportLogs->removeElement($exportLog)) {
            // set the owning side to null (unless already changed)
            if ($exportLog->getInventory() === $this) {
                $exportLog->setInventory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ImportLogs>
     */
    public function getImportLogs(): Collection
    {
        return $this->importLogs;
    }

    public function addImportLog(ImportLogs $importLog): static
    {
        if (!$this->importLogs->contains($importLog)) {
            $this->importLogs->add($importLog);
            $importLog->setInventory($this);
        }

        return $this;
    }

    public function removeImportLog(ImportLogs $importLog): static
    {
        if ($this->importLogs->removeElement($importLog)) {
            // set the owning side to null (unless already changed)
            if ($importLog->getInventory() === $this) {
                $importLog->setInventory(null);
            }
        }

        return $this;
    }

    // public function getName(): ?string
    // {
    //     return $this->name;
    // }

    // public function setName(string $name): static
    // {
    //     $this->name = $name;

    //     return $this;
    // }

    public function getEtablishment(): ?Etablishment
    {
        return $this->etablishment;
    }

    public function setEtablishment(?Etablishment $etablishment): static
    {
        $this->etablishment = $etablishment;
        return $this;
    }
}
