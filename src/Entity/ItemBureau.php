<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MappedSuperclass;

#[MappedSuperclass]
class ItemBureau
{

    #[ORM\OneToOne(inversedBy: 'item', cascade: ['persist', 'remove'])]
    private ?Bureau $bureau = null;

    public function getBureau(): ?Bureau
    {
        return $this->bureau;
    }

    public function setBureau(?Bureau $bureau): self
    {
        $this->bureau = $bureau;

        return $this;
    }

}