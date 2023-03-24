<?php

namespace App\Entity;

use App\Repository\PersonnelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Oh\GoogleMapFormTypeBundle\Traits\LocationTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PersonnelRepository::class)]
class Personnel
{
    use LocationTrait;
    const GENRE = ['Feminin', 'Masculin'];
    private $uploadRootDir;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getPersonnels"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getPersonnels"])]
    #[Assert\Length(min: 4)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getPersonnels"])]
    #[Assert\Length(min: 4)]
    private ?string $prenoms = null;

    #[ORM\Column(length: 20)]
    #[Groups(["getPersonnels"])]
    #[Assert\Choice(choices: self::GENRE, message: 'Choose a valid Genre.')]
    private ?string $genre = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?User $account = null;

    #[ORM\Column]
    private ?bool $status = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userCreated = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options:['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $dateCreated = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateNaiss = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $lieuNaiss = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Length(min:9)]
    private ?string $numeroCni = null;

    #[ORM\Column(length: 255)]
    private ?string $profession = null;

    #[ORM\Column(length: 255)]
    private ?string $allergies = null;

    #[ORM\Column(length: 20)]
    private ?string $groupeSangin = null;

    #[ORM\Column(length: 20)]
    #[Assert\Regex(
        pattern: '/^237[0-9]{9}+$/i',
        htmlPattern: '^(237[0-9]{9})$'
    )]
    private ?string $phoneMobile = null;

    #[ORM\Column(length: 20)]
    #[Assert\Regex(
        pattern: '/^237[0-9]{9}+$/i',
        htmlPattern: '^(237[0-9]{9})$'
    )]
    private ?string $phoneWhatsapp = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $personneContactNom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $personneContactAdresse = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $personneContactPhone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $personneContactQualite = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatarNomFichier = null;

    #[ORM\OneToMany(mappedBy: 'personnel', targetEntity: Contrat::class, orphanRemoval: true)]
    private Collection $contrats;

    #[ORM\OneToMany(mappedBy: 'personnel', targetEntity: Licence::class, orphanRemoval: true)]
    private Collection $licences;

    #[Assert\File(maxSize: '4096k', maxSizeMessage: 'Image trop lourde, maximum 4Mo', mimeTypes:['image/jpg', 'image/jpeg', 'image/gif', 'image/png'], mimeTypesMessage:'seule les fichiers jpg, jpeg, png et gif sont acceptes')]
    private ?UploadedFile $imageFile = null;

    #[Assert\File(maxSize: '2048k', maxSizeMessage: 'Pdf trop lourde, maximum 2Mo', mimeTypes:['application/pdf'], mimeTypesMessage:'seule les fichiers pdf sont acceptes')]
    private ?UploadedFile $cniScanFile = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $cniDeliverOn = null;

    #[ORM\Column(length: 255)]
    private ?string $cniDeliverAt = null;

    #[ORM\Column(length: 255)]
    private ?string $cniSignedBy = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cniScanFileName = null;

    #[ORM\OneToMany(mappedBy: 'personne', targetEntity: CertificatAptitude::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $certificatAptitudes;

    #[ORM\OneToMany(mappedBy: 'personne', targetEntity: Diplome::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $diplomes;

    #[ORM\OneToMany(mappedBy: 'personnel', targetEntity: PersonnelPosition::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $personnelPositions;

    #[ORM\OneToMany(mappedBy: 'personne', targetEntity: BureauPersonnes::class, orphanRemoval: true)]
    private Collection $bureauPersonnes;


    public function __toString()
    {
        return $this->getFullName();
    }

    public function __construct()
    {
        $this->contrats = new ArrayCollection();
        $this->licences = new ArrayCollection();
        $this->certificatAptitudes = new ArrayCollection();
        $this->diplomes = new ArrayCollection();
        $this->personnelPositions = new ArrayCollection();
        $this->bureauPersonnes = new ArrayCollection();
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

    public function getPrenoms(): ?string
    {
        return $this->prenoms;
    }

    public function setPrenoms(string $prenoms): self
    {
        $this->prenoms = $prenoms;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getAccount(): ?User
    {
        return $this->account;
    }

    public function setAccount(?User $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

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

    public function getDateNaiss(): ?\DateTimeInterface
    {
        return $this->dateNaiss;
    }

    public function setDateNaiss(\DateTimeInterface $dateNaiss): self
    {
        $this->dateNaiss = $dateNaiss;

        return $this;
    }

    public function getLieuNaiss(): ?string
    {
        return $this->lieuNaiss;
    }

    public function setLieuNaiss(string $lieuNaiss): self
    {
        $this->lieuNaiss = $lieuNaiss;

        return $this;
    }

    public function getNumeroCni(): ?string
    {
        return $this->numeroCni;
    }

    public function setNumeroCni(?string $numeroCni): self
    {
        $this->numeroCni = $numeroCni;

        return $this;
    }

    public function getProfession(): ?string
    {
        return $this->profession;
    }

    public function setProfession(string $profession): self
    {
        $this->profession = $profession;

        return $this;
    }

    public function getAllergies(): ?string
    {
        return $this->allergies;
    }

    public function setAllergies(string $allergies): self
    {
        $this->allergies = $allergies;

        return $this;
    }

    public function getGroupeSangin(): ?string
    {
        return $this->groupeSangin;
    }

    public function setGroupeSangin(string $groupeSangin): self
    {
        $this->groupeSangin = $groupeSangin;

        return $this;
    }

    public function getPhoneMobile(): ?string
    {
        return $this->phoneMobile;
    }

    public function setPhoneMobile(string $phoneMobile): self
    {
        $this->phoneMobile = $phoneMobile;

        return $this;
    }

    public function getPhoneWhatsapp(): ?string
    {
        return $this->phoneWhatsapp;
    }

    public function setPhoneWhatsapp(string $phoneWhatsapp): self
    {
        $this->phoneWhatsapp = $phoneWhatsapp;

        return $this;
    }

    public function getPersonneContactNom(): ?string
    {
        return $this->personneContactNom;
    }

    public function setPersonneContactNom(?string $personneContactNom): self
    {
        $this->personneContactNom = $personneContactNom;

        return $this;
    }

    public function getPersonneContactAdresse(): ?string
    {
        return $this->personneContactAdresse;
    }

    public function setPersonneContactAdresse(?string $personneContactAdresse): self
    {
        $this->personneContactAdresse = $personneContactAdresse;

        return $this;
    }

    public function getPersonneContactPhone(): ?string
    {
        return $this->personneContactPhone;
    }

    public function setPersonneContactPhone(?string $personneContactPhone): self
    {
        $this->personneContactPhone = $personneContactPhone;

        return $this;
    }

    public function getPersonneContactQualite(): ?string
    {
        return $this->personneContactQualite;
    }

    public function setPersonneContactQualite(?string $personneContactQualite): self
    {
        $this->personneContactQualite = $personneContactQualite;

        return $this;
    }

    public function getAvatarNomFichier(): ?string
    {
        return $this->avatarNomFichier;
    }

    public function setAvatarNomFichier(?string $avatarNomFichier): self
    {
        $this->avatarNomFichier = $avatarNomFichier;

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
            $contrat->setPersonnel($this);
        }

        return $this;
    }

    public function removeContrat(Contrat $contrat): self
    {
        if ($this->contrats->removeElement($contrat)) {
            // set the owning side to null (unless already changed)
            if ($contrat->getPersonnel() === $this) {
                $contrat->setPersonnel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Licence>
     */
    public function getLicences(): Collection
    {
        return $this->licences;
    }

    public function addLicence(Licence $licence): self
    {
        if (!$this->licences->contains($licence)) {
            $this->licences->add($licence);
            $licence->setPersonnel($this);
        }

        return $this;
    }

    public function removeLicence(Licence $licence): self
    {
        if ($this->licences->removeElement($licence)) {
            // set the owning side to null (unless already changed)
            if ($licence->getPersonnel() === $this) {
                $licence->setPersonnel(null);
            }
        }

        return $this;
    }

    public function getFullName(): string
    {
        return $this->prenoms.' '.$this->nom;
    }

    public function getAvatarWebPath(): string
    {
        return $this->getUploadDir().'/'.$this->avatarNomFichier;
    }

    public function getCniFileWebPath(): string
    {
        return $this->getUploadFileDir().'/'.$this->cniScanFileName;
    }

    public function getCniFileAbsolutePath()
    {
        return null === $this->cniScanFileName
            ? null
            : $this->getUploadFileRootDir().'/'.$this->cniScanFileName;
    }

    public function getAbsolutePath()
    {
        return null === $this->avatarNomFichier
            ? null
            : $this->getUploadRootDir().'/'.$this->avatarNomFichier;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../public'.$this->getUploadDir();
    }

    protected function getUploadFileRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../public'.$this->getUploadFileDir();
    }

    public function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return '/pictures';
    }

    public function getUploadFileDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return '/files';
    }

    public function removeAvatarFile()
    {
        $file = $this->getAbsolutePath();
        if ($file) {
            unlink($file);
        }
    }

    public function setCniScanFile(?UploadedFile $file = null): void
    {
        $this->cniScanFile = $file;
    }

    public function getCniScanFileFromName(){
        if($this->cniScanFile){
            return new UploadedFile($this->getCniFileAbsolutePath(), $this->cniScanFileName);
        }else{
            return null;
        }
    }

    public function getCniScanFile(): ?UploadedFile
    {
        return $this->cniScanFile;
    }

    public function setImageFile(?UploadedFile $file = null): void
    {
        $this->imageFile = $file;
    }

    public function getFileFromName(){
        if($this->avatarNomFichier){
            return new UploadedFile($this->getAbsolutePath(), $this->avatarNomFichier);
        }else{
            return null;
        }
    }

    public function getImageFile(): ?UploadedFile
    {
        return $this->imageFile;
    }

    public function getCniDeliverOn(): ?\DateTimeInterface
    {
        return $this->cniDeliverOn;
    }

    public function setCniDeliverOn(\DateTimeInterface $cniDeliverOn): self
    {
        $this->cniDeliverOn = $cniDeliverOn;

        return $this;
    }

    public function getCniDeliverAt(): ?string
    {
        return $this->cniDeliverAt;
    }

    public function setCniDeliverAt(string $cniDeliverAt): self
    {
        $this->cniDeliverAt = $cniDeliverAt;

        return $this;
    }

    public function getCniSignedBy(): ?string
    {
        return $this->cniSignedBy;
    }

    public function setCniSignedBy(string $cniSignedBy): self
    {
        $this->cniSignedBy = $cniSignedBy;

        return $this;
    }

    public function getCniScanFileName(): ?string
    {
        return $this->cniScanFileName;
    }

    public function setCniScanFileName(?string $cniScanFileName): self
    {
        $this->cniScanFileName = $cniScanFileName;

        return $this;
    }

    /**
     * @return Collection<int, CertificatAptitude>
     */
    public function getCertificatAptitudes(): Collection
    {
        return $this->certificatAptitudes;
    }

    public function addCertificatAptitude(CertificatAptitude $certificatAptitude): self
    {
        if (!$this->certificatAptitudes->contains($certificatAptitude)) {
            $this->certificatAptitudes->add($certificatAptitude);
            $certificatAptitude->setPersonne($this);
        }

        return $this;
    }

    public function removeCertificatAptitude(CertificatAptitude $certificatAptitude): self
    {
        if ($this->certificatAptitudes->removeElement($certificatAptitude)) {
            // set the owning side to null (unless already changed)
            if ($certificatAptitude->getPersonne() === $this) {
                $certificatAptitude->setPersonne(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Diplome>
     */
    public function getDiplomes(): Collection
    {
        return $this->diplomes;
    }

    public function addDiplome(Diplome $diplome): self
    {
        if (!$this->diplomes->contains($diplome)) {
            $this->diplomes->add($diplome);
            $diplome->setPersonne($this);
        }

        return $this;
    }

    public function removeDiplome(Diplome $diplome): self
    {
        if ($this->diplomes->removeElement($diplome)) {
            // set the owning side to null (unless already changed)
            if ($diplome->getPersonne() === $this) {
                $diplome->setPersonne(null);
            }
        }

        return $this;
    }

    public function getMyPositions(){
        $result = [];
        foreach ($this->personnelPositions as $personnePosition) {
            $result[] = $personnePosition->getPosition()->__toString();
        }
        return join(', ', $result);
    }

    /**
     * @return Collection<int, PersonnelPosition>
     */
    public function getPersonnelPositions(): Collection
    {
        return $this->personnelPositions;
    }

    public function addPersonnelPosition(PersonnelPosition $personnelPosition): self
    {
        if (!$this->personnelPositions->contains($personnelPosition)) {
            $this->personnelPositions->add($personnelPosition);
            $personnelPosition->setPersonnel($this);
        }

        return $this;
    }

    public function removePersonnelPosition(PersonnelPosition $personnelPosition): self
    {
        if ($this->personnelPositions->removeElement($personnelPosition)) {
            // set the owning side to null (unless already changed)
            if ($personnelPosition->getPersonnel() === $this) {
                $personnelPosition->setPersonnel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BureauPersonnes>
     */
    public function getBureauPersonnes(): Collection
    {
        return $this->bureauPersonnes;
    }

    public function addBureauPersonne(BureauPersonnes $bureauPersonne): self
    {
        if (!$this->bureauPersonnes->contains($bureauPersonne)) {
            $this->bureauPersonnes->add($bureauPersonne);
            $bureauPersonne->setPersonne($this);
        }

        return $this;
    }

    public function removeBureauPersonne(BureauPersonnes $bureauPersonne): self
    {
        if ($this->bureauPersonnes->removeElement($bureauPersonne)) {
            // set the owning side to null (unless already changed)
            if ($bureauPersonne->getPersonne() === $this) {
                $bureauPersonne->setPersonne(null);
            }
        }

        return $this;
    }

}
