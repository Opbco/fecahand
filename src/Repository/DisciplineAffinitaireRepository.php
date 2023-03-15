<?php

namespace App\Repository;

use App\Entity\DisciplineAffinitaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DisciplineAffinitaire>
 *
 * @method DisciplineAffinitaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method DisciplineAffinitaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method DisciplineAffinitaire[]    findAll()
 * @method DisciplineAffinitaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DisciplineAffinitaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DisciplineAffinitaire::class);
    }

    public function save(DisciplineAffinitaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DisciplineAffinitaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DisciplineAffinitaire[] Returns an array of DisciplineAffinitaire objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DisciplineAffinitaire
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
