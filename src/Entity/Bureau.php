<?php

namespace App\Entity;

use App\Repository\BureauRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Nullable;
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
    #[Assert\DateTime]
    private ?\DateTimeInterface $dateElection = null;

    #[ORM\Column]
    private ?bool $actif = null;

    #[ORM\OneToOne(mappedBy: 'bureau', cascade: ['persist', 'remove'])]
    private ?ItemBureau $item = null;

    #[ORM\OneToMany(mappedBy: 'bureau', targetEntity: BureauPosition::class, orphanRemoval: true, fetch:"EXTRA_LAZY")]
    private Collection $positions;

    public function __construct()
    {
        $this->positions = new ArrayCollection();
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
}
