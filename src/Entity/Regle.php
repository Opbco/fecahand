<?php

namespace App\Entity;

use App\Repository\RegleRepository;
use App\Trait\PdfTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RegleRepository::class)]
class Regle
{
    use PdfTrait; 
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $datePromulgation = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userCreated = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateCreated = null;

    #[ORM\OneToMany(mappedBy: 'regle', targetEntity: DisciplineRegles::class, orphanRemoval: true)]
    private Collection $disciplineRegles;

    public function __construct()
    {
        $this->disciplineRegles = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDatePromulgation(): ?\DateTimeInterface
    {
        return $this->datePromulgation;
    }

    public function setDatePromulgation(\DateTimeInterface $datePromulgation): self
    {
        $this->datePromulgation = $datePromulgation;

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

    public function getUserCreated(): ?User
    {
        return $this->userCreated;
    }

    public function setUserCreated(?User $userCreated): self
    {
        $this->userCreated = $userCreated;

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
     * @return Collection<int, DisciplineRegles>
     */
    public function getDisciplineRegles(): Collection
    {
        return $this->disciplineRegles;
    }

    public function addDisciplineRegle(DisciplineRegles $disciplineRegle): self
    {
        if (!$this->disciplineRegles->contains($disciplineRegle)) {
            $this->disciplineRegles->add($disciplineRegle);
            $disciplineRegle->setRegle($this);
        }

        return $this;
    }

    public function removeDisciplineRegle(DisciplineRegles $disciplineRegle): self
    {
        if ($this->disciplineRegles->removeElement($disciplineRegle)) {
            // set the owning side to null (unless already changed)
            if ($disciplineRegle->getRegle() === $this) {
                $disciplineRegle->setRegle(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getNom();
    }
}
