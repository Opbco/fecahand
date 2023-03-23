<?php

namespace App\Entity;

use App\Repository\InsuranceRepository;
use App\Trait\PdfTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InsuranceRepository::class)]
class Insurance
{
    use PdfTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT, options:['default' => 1])]
    private ?int $status = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $numero = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $typeAssurance = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateDelivrance = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateExpiration = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $deliveredAt = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $deliveredBy = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userCreated = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options:['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $dateCreated = null;

    #[ORM\ManyToOne(inversedBy: 'insurances')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Club $club = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getTypeAssurance(): ?string
    {
        return $this->typeAssurance;
    }

    public function setTypeAssurance(string $typeAssurance): self
    {
        $this->typeAssurance = $typeAssurance;

        return $this;
    }

    public function getDateDelivrance(): ?\DateTimeInterface
    {
        return $this->dateDelivrance;
    }

    public function setDateDelivrance(\DateTimeInterface $dateDelivrance): self
    {
        $this->dateDelivrance = $dateDelivrance;

        return $this;
    }

    public function getDateExpiration(): ?\DateTimeInterface
    {
        return $this->dateExpiration;
    }

    public function setDateExpiration(\DateTimeInterface $dateExpiration): self
    {
        $this->dateExpiration = $dateExpiration;

        return $this;
    }

    public function getDeliveredAt(): ?string
    {
        return $this->deliveredAt;
    }

    public function setDeliveredAt(string $deliveredAt): self
    {
        $this->deliveredAt = $deliveredAt;

        return $this;
    }

    public function getDeliveredBy(): ?string
    {
        return $this->deliveredBy;
    }

    public function setDeliveredBy(string $deliveredBy): self
    {
        $this->deliveredBy = $deliveredBy;

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

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getClub(): ?Club
    {
        return $this->club;
    }

    public function setClub(?Club $club): self
    {
        $this->club = $club;

        return $this;
    }

    /**
     * @return $this
     */
    public function setTypeAssMul($typeAss)
    {
        $this->setTypeAssurance(implode(', ', $typeAss));
        return $this;
    }

    public function getTypeAssMul()
    {
        return explode(', ', $this->getTypeAssurance());

    }
}
