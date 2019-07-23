<?php

namespace App\Repository;

use App\Entity\PictureVehicle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PictureVehicle|null find($id, $lockMode = null, $lockVersion = null)
 * @method PictureVehicle|null findOneBy(array $criteria, array $orderBy = null)
 * @method PictureVehicle[]    findAll()
 * @method PictureVehicle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PictureVehicleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PictureVehicle::class);
    }

    // /**
    //  * @return PictureVehicle[] Returns an array of PictureVehicle objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PictureVehicle
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
