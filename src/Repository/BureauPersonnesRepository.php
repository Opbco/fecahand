<?php

namespace App\Repository;

use App\Entity\BureauPersonnes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BureauPersonnes>
 *
 * @method BureauPersonnes|null find($id, $lockMode = null, $lockVersion = null)
 * @method BureauPersonnes|null findOneBy(array $criteria, array $orderBy = null)
 * @method BureauPersonnes[]    findAll()
 * @method BureauPersonnes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BureauPersonnesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BureauPersonnes::class);
    }

    public function save(BureauPersonnes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BureauPersonnes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return BureauPersonnes[] Returns an array of BureauPersonnes objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BureauPersonnes
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
