<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name:'item_type', type:'string')]
#[ORM\DiscriminatorMap(['one'=>'Club', 'two'=>'League', 'three'=>'DisciplineAffinitaire'])]
class ItemBureau
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'item', cascade: ['persist', 'remove'])]
    protected ?Bureau $bureau = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function __toString()
    {
        return $this->getBureau();
    }

}