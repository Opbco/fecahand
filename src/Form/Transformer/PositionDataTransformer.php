<?php

namespace App\Form\Transformer;

use App\Entity\Bureau;
use App\Entity\BureauPosition;
use Doctrine\Common\Collections\ArrayCollection;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use Symfony\Component\Form\DataTransformerInterface;

final class PositionDataTransformer implements DataTransformerInterface
{
    private Bureau $bureau;

    private ModelManager $modelManager;

    public function __construct(Bureau $bureau, ModelManager $modelManager)
    {
        $this->bureau         = $bureau;
        $this->modelManager = $modelManager;
    }

    public function transform($value)
    {
        if (!is_null($value)) {
            $results = [];

            /** @var BureauPosition $bureauPositions */
            foreach ($value as $bureauPosition) {
                $results[] = $bureauPosition->getPosition();
            }

            return $results;
        }

        return $value;
    }

    public function reverseTransform($value)
    {
        $results  = new ArrayCollection();
        $position = 0;

        /** @var Position $position */
        foreach ($value as $position) {
            $bureauPosition = $this->create();
            $bureauPosition->setPosition($position);
            $bureauPosition->setNombre(1);

            $results->add($bureauPosition);
        }

        // Remove Old values
        $qb   = $this->modelManager->getEntityManager(BureauPosition::class)->createQueryBuilder();
        $expr = $this->modelManager->getEntityManager(BureauPosition::class)->getExpressionBuilder();

        $bureauPositionToRemove = $qb->select('entity')
                                           ->from(BureauPosition::class, 'entity')
                                           ->where($expr->eq('entity.bureau', $this->bureau->getId()))
                                           ->getQuery()
                                           ->getResult();

        foreach ($bureauPositionToRemove as $bureauPosition) {
            $this->modelManager->delete($bureauPosition, false);
        }

        $this->modelManager->getEntityManager(BureauPosition::class)->flush();

        return $results;
    }
}