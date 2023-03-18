<?php

namespace App\Repository;

use App\Entity\PersonnelPosition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PersonnelPosition>
 *
 * @method PersonnelPosition|null find($id, $lockMode = null, $lockVersion = null)
 * @method PersonnelPosition|null findOneBy(array $criteria, array $orderBy = null)
 * @method PersonnelPosition[]    findAll()
 * @method PersonnelPosition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonnelPositionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonnelPosition::class);
    }

    public function save(PersonnelPosition $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PersonnelPosition $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PersonnelPosition[] Returns an array of PersonnelPosition objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PersonnelPosition
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
