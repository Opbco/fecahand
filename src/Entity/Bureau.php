<?php

namespace App\Entity;

use App\Repository\BureauRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BureauRepository::class)]
class Bureau
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 10)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\Type('dateTime')]
    private ?\DateTimeInterface $dateElection = null;

    #[ORM\Column]
    private ?bool $actif = null;

    #[ORM\OneToOne(targetEntity: ItemBureau::class, cascade:['persist'])]
    #[ORM\JoinColumn(name:'item_id', referencedColumnName:'id')]
    private ?ItemBureau $item = null;

    #[ORM\OneToMany(mappedBy: 'bureau', targetEntity: BureauPosition::class, orphanRemoval: true, fetch:"EXTRA_LAZY", cascade:['persist', 'remove'])]
    private Collection $positions;

    #[ORM\OneToMany(mappedBy: 'bureau', targetEntity: BureauPersonnes::class, orphanRemoval: true, cascade:['persist', 'remove'])]
    private Collection $personnes;

    public function __construct()
    {
        $this->positions = new ArrayCollection();
        $this->personnes = new ArrayCollection();
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

    public function getDateElection(): ?\DateTimeInterface
    {
        return $this->dateElection;
    }

    public function setDateElection(\DateTimeInterface $dateElection): self
    {
        $this->dateElection = $dateElection;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getItem(): ?Club
    {
        return $this->item;
    }

    public function setItem(?ItemBureau $item): self
    {
        // unset the owning side of the relation if necessary
        if ($item === null && $this->item !== null) {
            $this->item->setBureau(null);
        }

        // set the owning side of the relation if necessary
        if ($item !== null && $item->getBureau() !== $this) {
            $item->setBureau($this);
        }

        $this->item = $item;

        return $this;
    }

    /**
     * @return Collection<int, BureauPosition>
     */
    public function getPositions(): Collection
    {
        return $this->positions;
    }

    public function addPosition(BureauPosition $position): self
    {
        if (!$this->positions->contains($position)) {
            $this->positions->add($position);
            $position->setBureau($this);
        }

        return $this;
    }

    public function removePosition(BureauPosition $position): self
    {
        if ($this->positions->removeElement($position)) {
            // set the owning side to null (unless already changed)
            if ($position->getBureau() === $this) {
                $position->setBureau(null);
            }
        }

        return $this;
    }

    public function setPositions(Collection $positions): void
    {
        $this->positions = new ArrayCollection;

        foreach ($positions as $one) {
            $this->addPosition($one);
        }
    }

    public function __toString()
    {
        return $this->getNom();
    }

    /**
     * @return Collection<int, BureauPersonnes>
     */
    public function getPersonnes(): Collection
    {
        return $this->personnes;
    }

    public function addPersonne(BureauPersonnes $personne): self
    {
        if (!$this->personnes->contains($personne)) {
            $this->personnes->add($personne);
            $personne->setBureau($this);
        }

        return $this;
    }

    public function removePersonne(BureauPersonnes $personne): self
    {
        if ($this->personnes->removeElement($personne)) {
            // set the owning side to null (unless already changed)
            if ($personne->getBureau() === $this) {
                $personne->setBureau(null);
            }
        }

        return $this;
    }
}
