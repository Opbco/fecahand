<?php

namespace App\Entity;

use App\Repository\PersonnelPositionRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonnelPositionRepository::class)]
#[UniqueEntity(
    fields: ['personnel', 'position'],
    errorPath: 'position',
    message: 'Cette position est deja attribuee.',
)]
class PersonnelPosition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'personnelPositions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Personnel $personnel = null;

    #[ORM\ManyToOne(inversedBy: 'positionPersonnels')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Position $position = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPersonnel(): ?Personnel
    {
        return $this->personnel;
    }

    public function setPersonnel(?Personnel $personnel): self
    {
        $this->personnel = $personnel;

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

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getPosition();
    }
}
