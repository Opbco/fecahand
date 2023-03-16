<?php

namespace App\Entity;

use App\Repository\DiplomeRepository;
use App\Trait\PdfTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiplomeRepository::class)]
class Diplome
{
    use PdfTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $deliveryDate = null;

    #[ORM\Column(length: 255)]
    private ?string $institution = null;

    #[ORM\ManyToOne(inversedBy: 'diplomes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Personnel $personne = null;

    public function __toString()
    {
        return $this->nom.' ('.$this->institution.')';
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

    public function getDeliveryDate(): ?\DateTimeInterface
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(\DateTimeInterface $deliveryDate): self
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    public function getInstitution(): ?string
    {
        return $this->institution;
    }

    public function setInstitution(string $institution): self
    {
        $this->institution = $institution;

        return $this;
    }

    public function getPersonne(): ?Personnel
    {
        return $this->personne;
    }

    public function setPersonne(?Personnel $personne): self
    {
        $this->personne = $personne;

        return $this;
    }
}
