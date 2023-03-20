<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\MappedSuperclass;

#[MappedSuperclass]
class Status
{
    const STATUS_TRANSMIT = 1;
    const STATUS_TRAITEMENT = 2;
    const STATUS_VALIDE = 3;

    public static $statusCodes = array(
        "Transmit" => Status::STATUS_TRANSMIT,
        "Traitement" => Status::STATUS_TRAITEMENT,
        "Valide" => Status::STATUS_VALIDE
    );

    public static $codesStatus = array(
        Status::STATUS_TRANSMIT => "Transmit",
        Status::STATUS_TRAITEMENT => "Traitement",
        Status::STATUS_VALIDE => "Valide"
    );

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\Positive]
    private ?int $status = null;

    public static function getStatusCodes()
    {
        return self::$statusCodes;
    }

    public function getStatus(): ?string
    {
        if (!is_null($this->status)) {
            return self::$codesStatus[$this->status];
        } else {
            return null;
        }
    }

    public function setStatus(int $status): self
    {
        $this->status = intval($status);

        return $this;
    }
}