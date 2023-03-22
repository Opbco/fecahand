<?php

namespace App\Repository;

use App\Entity\DisciplineRegles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DisciplineRegles>
 *
 * @method DisciplineRegles|null find($id, $lockMode = null, $lockVersion = null)
 * @method DisciplineRegles|null findOneBy(array $criteria, array $orderBy = null)
 * @method DisciplineRegles[]    findAll()
 * @method DisciplineRegles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DisciplineReglesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DisciplineRegles::class);
    }

    public function save(DisciplineRegles $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DisciplineRegles $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DisciplineRegles[] Returns an array of DisciplineRegles objects
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

//    public function findOneBySomeField($value): ?DisciplineRegles
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
