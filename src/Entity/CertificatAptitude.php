<?php

namespace App\Entity;

use App\Repository\CertificatAptitudeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CertificatAptitudeRepository::class)]
class CertificatAptitude
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $deliveryDate = null;

    #[ORM\Column(length: 255)]
    private ?string $deliveryBy = null;

    #[ORM\Column(length: 255)]
    private ?string $deliveryAt = null;

    #[ORM\Column(length: 255)]
    private ?string $remarks = null;

    #[ORM\ManyToOne(inversedBy: 'certificatAptitudes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Personnel $personne = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDeliveryBy(): ?string
    {
        return $this->deliveryBy;
    }

    public function setDeliveryBy(string $deliveryBy): self
    {
        $this->deliveryBy = $deliveryBy;

        return $this;
    }

    public function getDeliveryAt(): ?string
    {
        return $this->deliveryAt;
    }

    public function setDeliveryAt(string $deliveryAt): self
    {
        $this->deliveryAt = $deliveryAt;

        return $this;
    }

    public function getRemarks(): ?string
    {
        return $this->remarks;
    }

    public function setRemarks(string $remarks): self
    {
        $this->remarks = $remarks;

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
