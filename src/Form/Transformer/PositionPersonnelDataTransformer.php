<?php

namespace App\Form\Transformer;

use App\Entity\PersonnelPosition;
use App\Entity\Personnel;
use Doctrine\Common\Collections\ArrayCollection;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use Symfony\Component\Form\DataTransformerInterface;

final class PositionPersonnelDataTransformer implements DataTransformerInterface
{
    private Personnel $personne;

    private ModelManager $modelManager;

    public function __construct(Personnel $personne, ModelManager $modelManager)
    {
        $this->personne         = $personne;
        $this->modelManager = $modelManager;
    }

    public function transform($value)
    {
        
        if (!is_null($value)) {
            $results = [];

            /** @var Position $personnePosition */
            foreach ($value as $personnePosition) {
                $results[] = $personnePosition->getPosition();
            }

            return $results;
        }

        return $value;
    }

    public function reverseTransform($value)
    {
        return $value;
    }
}