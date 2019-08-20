<?php

namespace App\Repository;

use App\Entity\Vehicle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Vehicle|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vehicle|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vehicle[]    findAll()
 * @method Vehicle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VehicleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Vehicle::class);
    }

    // /**
    //  * @return Vehicle[] Returns an array of Vehicle objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Vehicle
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findVehiclesAvailable(array $ids)
    {
        $query = $this->createQueryBuilder('v')
            ->andWhere('v.id not IN (:ids)')
            ->setParameter('ids', $ids);
        return $query->getQuery()
            ->getResult();
    }

    public function findAllTypeVehicle(){
        $query = $this->createQueryBuilder('v')
            ->select('DISTINCT v.type')
            ->orderBy('v.type');

        return $query->getQuery()->getResult();
    }

    public function findAllBrandVehicle(string $type){
        $query = $this->createQueryBuilder('v')
            ->select('DISTINCT v.brand')
            ->andWhere('v.type LIKE :type')
            ->setParameter('type',"%$type%")
            ->orderBy('v.brand');

        return $query->getQuery()->getResult();
    }

    public function findAllModelVehicle(string $brand, string $type){
        $query = $this->createQueryBuilder('v')
            ->select('DISTINCT v.model')
            ->andWhere('v.brand LIKE :brand')
            ->andWhere('v.type LIKE :type')
            ->setParameter('brand',"%$brand%")
            ->setParameter('type',"%$type%")
            ->orderBy('v.model');

        return $query->getQuery()->getResult();
    }

    public function findSearchVehicle(string $type, string $brand, string $model){
        $query = $this->createQueryBuilder('v')
            ->select('v.id, v.brand, v.model, v.km, v.description')
            ->andWhere('v.brand LIKE :brand')
            ->andWhere('v.type LIKE :type')
            ->andWhere('v.model LIKE :model')
            ->setParameter('model',"%$model%")
            ->setParameter('brand',"%$brand%")
            ->setParameter('type',"%$type%")
            ->orderBy('v.id');

        return $query->getQuery()->getResult();
    }
}
