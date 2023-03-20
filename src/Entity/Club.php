<?php

namespace App\Entity;

use App\Repository\ClubRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Oh\GoogleMapFormTypeBundle\Traits\LocationTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClubRepository::class)]
class Club extends ItemBureau
{
    use LocationTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 10)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\Type('datetime')]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: Types::JSON)]
    private array $couleurs = [];

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 5)]
    private ?string $devise = null;

    #[ORM\Column(length: 255)]
    private ?string $numeroMinat = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\Type('datetime')]
    private ?\DateTimeInterface $datePublication = null;

    #[ORM\Column(type: Types::SMALLINT, nullable:true)]
    private ?int $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\Type('datetime')]
    private ?\DateTimeInterface $dateCreated = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\Type('datetime')]
    private ?\DateTimeInterface $dateUpdated = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userCreated = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userUpdated = null;

    #[ORM\OneToMany(mappedBy: 'club', targetEntity: Affiliation::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $affiliations;

    #[ORM\OneToMany(mappedBy: 'club', targetEntity: ClubStade::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $stades;

    #[ORM\OneToMany(mappedBy: 'club', targetEntity: Contrat::class, orphanRemoval: true)]
    private Collection $contrats;

    public function __construct()
    {
        $this->affiliations = new ArrayCollection();
        $this->stades = new ArrayCollection();
        $this->contrats = new ArrayCollection();
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

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getCouleurs(): array
    {
        return $this->couleurs;
    }

    public function setCouleurs(array $couleurs): self
    {
        $this->couleurs = $couleurs;

        return $this;
    }

    public function getDevise(): ?string
    {
        return $this->devise;
    }

    public function setDevise(string $devise): self
    {
        $this->devise = $devise;

        return $this;
    }

    public function getNumeroMinat(): ?string
    {
        return $this->numeroMinat;
    }

    public function setNumeroMinat(string $numeroMinat): self
    {
        $this->numeroMinat = $numeroMinat;

        return $this;
    }

    public function getDatePublication(): ?\DateTimeInterface
    {
        return $this->datePublication;
    }

    public function setDatePublication(\DateTimeInterface $datePublication): self
    {
        $this->datePublication = $datePublication;

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

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getDateUpdated(): ?\DateTimeInterface
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated(\DateTimeInterface $dateUpdated): self
    {
        $this->dateUpdated = $dateUpdated;

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

    public function getUserUpdated(): ?User
    {
        return $this->userUpdated;
    }

    public function setUserUpdated(?User $userUpdated): self
    {
        $this->userUpdated = $userUpdated;

        return $this;
    }

    /**
     * @return Collection<int, Affiliation>
     */
    public function getAffiliations(): Collection
    {
        return $this->affiliations;
    }

    public function addAffiliation(Affiliation $affiliation): self
    {
        if (!$this->affiliations->contains($affiliation)) {
            $this->affiliations->add($affiliation);
            $affiliation->setClub($this);
        }

        return $this;
    }

    public function removeAffiliation(Affiliation $affiliation): self
    {
        if ($this->affiliations->removeElement($affiliation)) {
            // set the owning side to null (unless already changed)
            if ($affiliation->getClub() === $this) {
                $affiliation->setClub(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getNom();
    }

    /**
     * @return Collection<int, CludStade>
     */
    public function getStade(): Collection
    {
        return $this->stades;
    }

    public function addStade(ClubStade $stade): self
    {
        if (!$this->stades->contains($stade)) {
            $this->stades->add($stade);
            $stade->setClub($this);
        }

        return $this;
    }

    public function removeStade(ClubStade $stade): self
    {
        if ($this->stades->removeElement($stade)) {
            // set the owning side to null (unless already changed)
            if ($stade->getClub() === $this) {
                $stade->setClub(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Contrat>
     */
    public function getContrats(): Collection
    {
        return $this->contrats;
    }

    public function addContrat(Contrat $contrat): self
    {
        if (!$this->contrats->contains($contrat)) {
            $this->contrats->add($contrat);
            $contrat->setClub($this);
        }

        return $this;
    }

    public function removeContrat(Contrat $contrat): self
    {
        if ($this->contrats->removeElement($contrat)) {
            // set the owning side to null (unless already changed)
            if ($contrat->getClub() === $this) {
                $contrat->setClub(null);
            }
        }

        return $this;
    }
}
