<?php

namespace App\Entity;

use App\Repository\DisciplineReglesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DisciplineReglesRepository::class)]
class DisciplineRegles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'disciplineRegles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DisciplineAffinitaire $discipline = null;

    #[ORM\ManyToOne(inversedBy: 'disciplineRegles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Regle $regle = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDiscipline(): ?DisciplineAffinitaire
    {
        return $this->discipline;
    }

    public function setDiscipline(?DisciplineAffinitaire $discipline): self
    {
        $this->discipline = $discipline;

        return $this;
    }

    public function getRegle(): ?Regle
    {
        return $this->regle;
    }

    public function setRegle(?Regle $regle): self
    {
        $this->regle = $regle;

        return $this;
    }
}
