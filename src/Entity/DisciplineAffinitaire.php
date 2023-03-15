<?php

namespace App\Entity;

use App\Repository\DisciplineAffinitaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: DisciplineAffinitaireRepository::class)]
class DisciplineAffinitaire extends ItemBureau
{
    const TYPE_BALL = ['Rond', 'Oval', 'Plat'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[ORM\Column]
    #[Assert\Positive]
    private ?int $nombreJoueur = null;

    #[ORM\Column]
    #[Assert\Positive]
    private ?int $nombreJoueurStade = null;

    #[ORM\Column(length: 255)]
    #[Assert\Choice(choices: self::TYPE_BALL, message: 'Choose a valid type of ball.')]
    private ?string $typeBalle = null;

    #[ORM\Column]
    #[Assert\Positive]
    private ?float $dimensionBalle = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $description = null;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => 0])]
    #[Assert\NotNull]
    private ?bool $active = null;

    #[ORM\Column]
    #[Assert\Positive]
    private ?int $dureeMandatSec = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, options:['default' => 'CURRENT_TIMESTAMP'])]
    #[Assert\DateTime]
    private ?\DateTimeInterface $dateCreated = null;

    #[ORM\ManyToMany(targetEntity: Regle::class, mappedBy: 'disciplines')]
    private Collection $regles;

    public function __construct()
    {
        $this->regles = new ArrayCollection();
    }

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

    public function getNombreJoueur(): ?int
    {
        return $this->nombreJoueur;
    }

    public function setNombreJoueur(int $nombreJoueur): self
    {
        $this->nombreJoueur = $nombreJoueur;

        return $this;
    }

    public function getNombreJoueurStade(): ?int
    {
        return $this->nombreJoueurStade;
    }

    public function setNombreJoueurStade(int $nombreJoueurStade): self
    {
        $this->nombreJoueurStade = $nombreJoueurStade;

        return $this;
    }

    public function getTypeBalle(): ?string
    {
        return $this->typeBalle;
    }

    public function setTypeBalle(string $typeBalle): self
    {
        $this->typeBalle = $typeBalle;

        return $this;
    }

    public function getDimensionBalle(): ?float
    {
        return $this->dimensionBalle;
    }

    public function setDimensionBalle(float $dimensionBalle): self
    {
        $this->dimensionBalle = $dimensionBalle;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    public function getDureeMandatSec(): ?int
    {
        return $this->dureeMandatSec;
    }

    public function setDureeMandatSec(int $dureeMandatSec): self
    {
        $this->dureeMandatSec = $dureeMandatSec;

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

    /**
     * @return Collection<int, Regle>
     */
    public function getRegles(): Collection
    {
        return $this->regles;
    }

    public function addRegle(Regle $regle): self
    {
        if (!$this->regles->contains($regle)) {
            $this->regles->add($regle);
            $regle->addDiscipline($this);
        }

        return $this;
    }

    public function removeRegle(Regle $regle): self
    {
        if ($this->regles->removeElement($regle)) {
            $regle->removeDiscipline($this);
        }

        return $this;
    }
}
