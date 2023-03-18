<?php

namespace App\Entity;

use App\Repository\LeagueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LeagueRepository::class)]
class League extends ItemBureau
{
    const TYPE_LEAGUE = ['Specialisee', 'Regionale', 'Departementale'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\Choice(choices: self::TYPE_LEAGUE, message: 'Choose a valid type.')]
    private ?string $typeLeague = null;

    #[ORM\ManyToOne(inversedBy: 'leagues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Departement $departement = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, options:['default' => 'CURRENT_TIMESTAMP'])]
    #[Assert\DateTime]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options:['default' => 'CURRENT_TIMESTAMP'])]
    #[Assert\DateTime]
    private ?\DateTimeInterface $dateCreated = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userCreated = null;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => 0])]
    #[Assert\NotNull]
    private ?bool $active = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getTypeLeague(): ?string
    {
        return $this->typeLeague;
    }

    public function setTypeLeague(string $typeLeague): self
    {
        $this->typeLeague = $typeLeague;

        return $this;
    }

    public function getDepartement(): ?Departement
    {
        return $this->departement;
    }

    public function setDepartement(?Departement $departement): self
    {
        $this->departement = $departement;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getUserCreated(): ?User
    {
        return $this->userCreated;
    }

    public function setUserCreated(?User $userCreated): self
    {
        $this->userCreated = $userCreated;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }
}
