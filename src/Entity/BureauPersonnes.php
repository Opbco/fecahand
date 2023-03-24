<?php

namespace App\Entity;

use App\Repository\BureauPersonnesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BureauPersonnesRepository::class)]
class BureauPersonnes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bureauPersonnes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Personnel $personne = null;

    #[ORM\ManyToOne(inversedBy: 'personnes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Bureau $bureau = null;

    #[ORM\ManyToOne(inversedBy: 'bureauPersonnes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Position $position = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getBureau(): ?Bureau
    {
        return $this->bureau;
    }

    public function setBureau(?Bureau $bureau): self
    {
        $this->bureau = $bureau;

        return $this;
    }

    public function getPosition(): ?Position
    {
        return $this->position;
    }

    public function setPosition(?Position $position): self
    {
        $this->position = $position;

        return $this;
    }
}
