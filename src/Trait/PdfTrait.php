<?php

namespace App\Trait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;


trait PdfTrait
{

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pdfNom = null;

    #[Assert\File(maxSize: '2048k', maxSizeMessage: 'Pdf trop lourde, maximum 2Mo', mimeTypes:['application/pdf'], mimeTypesMessage:'seule les fichiers pdf sont acceptes')]
    private ?UploadedFile $pdfFile = null;

    public function getPdfNom(): ?string
    {
        return $this->pdfNom;
    }

    public function setPdfNom(?string $pdfNom): self
    {
        $this->pdfNom = $pdfNom;

        return $this;
    }

    public function getPdfWebPath(): string
    {
        return $this->getUploadPdfDir().'/'.$this->pdfNom;
    }

    public function getPdfAbsolutePath()
    {
        return null === $this->pdfNom
            ? null
            : $this->getUploadPdfRootDir().'/'.$this->pdfNom;
    }

    protected function getUploadPdfRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../public'.$this->getUploadPdfDir();
    }

    public function getUploadPdfDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/pdf in the view.
        return '/files';
    }

    public function setPdfFile(?UploadedFile $file = null): void
    {
        $this->pdfFile = $file;
    }

    public function getPdfFileFromName(){
        if($this->pdfNom){
            return new UploadedFile($this->getPdfAbsolutePath(), $this->pdfNom);
        }else{
            return null;
        }
    }

    public function getPdfFile(): ?UploadedFile
    {
        return $this->pdfFile;
    }
}