<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
#[UniqueEntity(fields: ['username'], message: 'Ce nom d\'utilisateur est déjà utilisé')]
#[ApiResource]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

   
    
    #[ORM\ManyToOne(targetEntity: Etablishment::class, inversedBy: 'user')]
    private ?Etablishment $etablishment = null;

    /**
     * @var Collection<int, ImportLogs>
     */
    #[ORM\OneToMany(targetEntity: ImportLogs::class, mappedBy: 'user')]
    private Collection $ImportLogs;

    /**
     * @var Collection<int, ExportLogs>
     */
    #[ORM\OneToMany(targetEntity: ExportLogs::class, mappedBy: 'user')]
    private Collection $exportsLogs;


    public function __construct()
    {
        $this->ImportLogs = new ArrayCollection();
        $this->exportsLogs = new ArrayCollection();
    }

    public function __toString(): string
        {
            return $this->getusername();
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    


    /**
     * @return Collection<int, ImportLogs>
     */
    public function getImportLogs(): Collection
    {
        return $this->ImportLogs;
    }

    public function addImportLog(ImportLogs $importLog): static
    {
        if (!$this->ImportLogs->contains($importLog)) {
            $this->ImportLogs->add($importLog);
            $importLog->setUser($this);
        }

        return $this;
    }

    public function removeImportLog(ImportLogs $importLog): static
    {
        if ($this->ImportLogs->removeElement($importLog)) {
            // set the owning side to null (unless already changed)
            if ($importLog->getUser() === $this) {
                $importLog->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ExportLogs>
     */
    public function getExportsLogs(): Collection
    {
        return $this->exportsLogs;
    }

    public function addExportsLog(ExportLogs $exportsLog): static
    {
        if (!$this->exportsLogs->contains($exportsLog)) {
            $this->exportsLogs->add($exportsLog);
            $exportsLog->setUser($this);
        }

        return $this;
    }

    public function removeExportsLog(ExportLogs $exportsLog): static
    {
        if ($this->exportsLogs->removeElement($exportsLog)) {
            // set the owning side to null (unless already changed)
            if ($exportsLog->getUser() === $this) {
                $exportsLog->setUser(null);
            }
        }

        return $this;
    }

    public function getEtablishment(): ?Etablishment
    {
        return $this->etablishment;
    }

    public function setEtablishment(?Etablishment $etablishment): static
    {
        $this->etablishment = $etablishment;

        return $this;
    }

    // public function getEtablishment(): ?Etablishment
    // {
    //     return $this->etablishment;
    // }

    // public function setEtablishment(?Etablishment $etablishment): self
    // {
    //     $this->etablishment = $etablishment;
    //     return $this;
    // }
}
