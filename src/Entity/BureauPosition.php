<?php

namespace App\Entity;

use App\Repository\BureauPositionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BureauPositionRepository::class)]
#[UniqueEntity(
    fields: ['bureau', 'position'],
    errorPath: 'position',
    message: 'This position is already assigned to this bureau',
)]
class BureauPosition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT, options:['default'=>1])]
    #[Assert\Positive]
    private ?int $nombre = null;

    #[ORM\ManyToOne(inversedBy: 'bureaux')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Position $position = null;

    #[ORM\ManyToOne(inversedBy: 'positions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Bureau $bureau = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?int
    {
        return $this->nombre;
    }

    public function setNombre(int $nombre): self
    {
        $this->nombre = $nombre;

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

    public function getBureau(): ?Bureau
    {
        return $this->bureau;
    }

    public function setBureau(?Bureau $bureau): self
    {
        $this->bureau = $bureau;

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
