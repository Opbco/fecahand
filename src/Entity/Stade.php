<?php

namespace App\Entity;

use App\Repository\StadeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Oh\GoogleMapFormTypeBundle\Traits\LocationTrait;

#[ORM\Entity(repositoryClass: StadeRepository::class)]
class Stade
{

    use LocationTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column]
    private array $dimension = [];

    #[ORM\Column]
    private ?bool $encercle = null;

    #[ORM\Column(length: 255)]
    private ?string $comment = null;

    #[ORM\Column(type: Types::SMALLINT, nullable:true, options:['default'=>1])]
    private ?int $status = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userCreated = null;

    #[ORM\ManyToMany(targetEntity: Club::class, mappedBy: 'stades')]
    private Collection $clubs;

    public function __construct()
    {
        $this->clubs = new ArrayCollection();
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

    public function getDimension(): array
    {
        return $this->dimension;
    }

    public function setDimension(array $dimension): self
    {
        $this->dimension = $dimension;

        return $this;
    }

    public function isEncercle(): ?bool
    {
        return $this->encercle;
    }

    public function setEncercle(bool $encercle): self
    {
        $this->encercle = $encercle;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

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

    /**
     * @return Collection<int, Club>
     */
    public function getClubs(): Collection
    {
        return $this->clubs;
    }

    public function addClub(Club $club): self
    {
        if (!$this->clubs->contains($club)) {
            $this->clubs->add($club);
            $club->addStade($this);
        }

        return $this;
    }

    public function removeClub(Club $club): self
    {
        if ($this->clubs->removeElement($club)) {
            $club->removeStade($this);
        }

        return $this;
    }
}
