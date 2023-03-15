<?php

namespace App\Entity;

use App\Repository\PositionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PositionRepository::class)]
class Position
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?bool $responsable = null;

    #[ORM\Column]
    private ?bool $licenced = null;

    #[ORM\Column]
    private ?bool $insured = null;

    #[ORM\Column]
    private ?bool $apte = null;

    #[ORM\OneToMany(mappedBy: 'position', targetEntity: BureauPosition::class, orphanRemoval: true)]
    private Collection $bureaux;

    public function __construct()
    {
        $this->bureaux = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
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

    public function isResponsable(): ?bool
    {
        return $this->responsable;
    }

    public function setResponsable(bool $responsable): self
    {
        $this->responsable = $responsable;

        return $this;
    }

    public function isLicenced(): ?bool
    {
        return $this->licenced;
    }

    public function setLicenced(bool $licenced): self
    {
        $this->licenced = $licenced;

        return $this;
    }

    public function isInsured(): ?bool
    {
        return $this->insured;
    }

    public function setInsured(bool $insured): self
    {
        $this->insured = $insured;

        return $this;
    }

    public function isApte(): ?bool
    {
        return $this->apte;
    }

    public function setApte(bool $apte): self
    {
        $this->apte = $apte;

        return $this;
    }

    /**
     * @return Collection<int, BureauPosition>
     */
    public function getBureaux(): Collection
    {
        return $this->bureaux;
    }

    public function addBureau(BureauPosition $bureau): self
    {
        if (!$this->bureaux->contains($bureau)) {
            $this->bureaux->add($bureau);
            $bureau->setPosition($this);
        }

        return $this;
    }

    public function removeBureau(BureauPosition $bureau): self
    {
        if ($this->bureaux->removeElement($bureau)) {
            // set the owning side to null (unless already changed)
            if ($bureau->getPosition() === $this) {
                $bureau->setPosition(null);
            }
        }

        return $this;
    }
}
