<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\StatisticsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatisticsRepository::class)]
#[ApiResource]
class Statistics
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $totalItem = null;

    #[ORM\Column(length: 255)]
    private ?string $itemRecently = null;

    #[ORM\Column(length: 255)]
    private ?string $lastUpdated = null;

    #[ORM\Column]
    private ?int $totalItemSalle = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalItem(): ?int
    {
        return $this->totalItem;
    }

    public function setTotalItem(int $totalItem): static
    {
        $this->totalItem = $totalItem;

        return $this;
    }

    public function getItemRecently(): ?string
    {
        return $this->itemRecently;
    }

    public function setItemRecently(string $itemRecently): static
    {
        $this->itemRecently = $itemRecently;

        return $this;
    }

    public function getLastUpdated(): ?string
    {
        return $this->lastUpdated;
    }

    public function setLastUpdated(string $lastUpdated): static
    {
        $this->lastUpdated = $lastUpdated;

        return $this;
    }

    public function getTotalItemSalle(): ?int
    {
        return $this->totalItemSalle;
    }

    public function setTotalItemSalle(int $totalItemSalle): static
    {
        $this->totalItemSalle = $totalItemSalle;

        return $this;
    }
}
