<?php

namespace App\Trait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;


trait ImageTrait
{

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageNom = null;

    #[Assert\File(maxSize: '4096k', maxSizeMessage: 'Image trop lourde, maximum 4Mo', mimeTypes:['image/jpg', 'image/jpeg', 'image/gif', 'image/png'], mimeTypesMessage:'seule les fichiers jpg, jpeg, png et gif sont acceptes')]
    private ?UploadedFile $imageFile = null;

    public function getImageNom(): ?string
    {
        return $this->imageNom;
    }

    public function setImageNom(?string $imageNom): self
    {
        $this->imageNom = $imageNom;

        return $this;
    }

    public function getImageWebPath(): string
    {
        return $this->getUploadImageDir().'/'.$this->imageNom;
    }

    public function getAvatarWebPath(): string
    {
        return $this->getImageWebPath();
    }

    public function getImageAbsolutePath()
    {
        return null === $this->imageNom
            ? null
            : $this->getUploadImageRootDir().'/'.$this->imageNom;
    }

    protected function getUploadImageRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../public'.$this->getUploadImageDir();
    }

    public function getUploadImageDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return '/pictures';
    }

    public function setImageFile(?UploadedFile $file = null): void
    {
        $this->imageFile = $file;
    }

    public function getImageFileFromName(){
        if($this->imageNom){
            return new UploadedFile($this->getImageAbsolutePath(), $this->imageNom);
        }else{
            return null;
        }
    }

    public function getImageFile(): ?UploadedFile
    {
        return $this->imageFile;
    }
}