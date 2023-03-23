<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\MappedSuperclass;

#[MappedSuperclass]
class Gender
{
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;
    const GENDER_MIX = 3;

    public static $genderCodes = array(
        "Homme" => Gender::GENDER_MALE,
        "Femme" => Gender::GENDER_FEMALE,
        "Mix" => Gender::GENDER_MIX
    );

    public static $codesGender = array(
        Gender::GENDER_MALE => "Homme",
        Gender::GENDER_FEMALE => "Femme",
        Gender::GENDER_MIX => "Mix"
    );

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\Positive]
    private ?int $genre = null;

    public static function getGenreCodes()
    {
        return self::$genderCodes;
    }

    public function getGenre(): ?string
    {
        if (!is_null($this->genre)) {
            return self::$codesGender[$this->genre];
        } else {
            return null;
        }
    }

    public function setGenre(int $genre): self
    {
        $this->genre = intval($genre);

        return $this;
    }
}