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
    private int $totalBlack = 0;

    #[ORM\Column]
    private int $totalColor = 0;

    #[ORM\Column]
    private int $totalScans = 0;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

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

    public function getTotalBlack(): int
    {
        return $this->totalBlack;
    }

    public function setTotalBlack(int $totalBlack): static
    {
        $this->totalBlack = $totalBlack;
        return $this;
    }

    public function getTotalColor(): int
    {
        return $this->totalColor;
    }

    public function setTotalColor(int $totalColor): static
    {
        $this->totalColor = $totalColor;
        return $this;
    }

    public function getTotalScans(): int
    {
        return $this->totalScans;
    }

    public function setTotalScans(int $totalScans): static
    {
        $this->totalScans = $totalScans;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function addBlack(int $amount): static
    {
        $this->totalBlack += $amount;
        return $this;
    }

    public function addColor(int $amount): static
    {
        $this->totalColor += $amount;
        return $this;
    }

    public function addScans(int $amount): static
    {
        $this->totalScans += $amount;
        return $this;
    }
}
