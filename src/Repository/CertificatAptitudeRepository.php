<?php

namespace App\Repository;

use App\Entity\CertificatAptitude;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CertificatAptitude>
 *
 * @method CertificatAptitude|null find($id, $lockMode = null, $lockVersion = null)
 * @method CertificatAptitude|null findOneBy(array $criteria, array $orderBy = null)
 * @method CertificatAptitude[]    findAll()
 * @method CertificatAptitude[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CertificatAptitudeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CertificatAptitude::class);
    }

    public function save(CertificatAptitude $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CertificatAptitude $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return CertificatAptitude[] Returns an array of CertificatAptitude objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CertificatAptitude
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
