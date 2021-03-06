<?php

namespace App\Repository;

use App\Entity\Location;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Location|null find($id, $lockMode = null, $lockVersion = null)
 * @method Location|null findOneBy(array $criteria, array $orderBy = null)
 * @method Location[]    findAll()
 * @method Location[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Location::class);
    }

    // /**
    //  * @return Location[] Returns an array of Location objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Location
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findVehiclesLocation(\DateTime $now){
        $query = $this->createQueryBuilder('l')
            ->join('l.vehicle','v')
            ->andWhere('l.startAt <= :now')
            ->andWhere('l.endAt >= :now')
            ->orWhere('l.returnAt is null')
            ->setParameter('now',$now)
            ->orderBy('v.id')
            ->groupBy('v.id');

        return $query->getQuery()
            ->getResult();
    }

    public function findVehiclesLocationOwner(\DateTime $now, User $user){
        $query = $this->createQueryBuilder('l')
            ->join('l.vehicle','v')
            ->andWhere('l.user != :user')
            ->andWhere('v.user = :user')
            ->andWhere('l.startAt <= :now')
            ->andWhere('l.endAt >= :now')
            ->orWhere('l.returnAt is null')
            ->setParameter('now',$now)
            ->setParameter('user', $user)
            ->orderBy('v.id')
            ->groupBy('v.id');

        return $query->getQuery()
            ->getResult();
    }

    public function findVehiclesLocationOwnerUser(\DateTime $now, User $user){
        $query = $this->createQueryBuilder('l')
            ->join('l.vehicle','v')
            ->andWhere('l.user = :user')
            ->andWhere('v.user != :user')
            ->andWhere('l.startAt <= :now')
            ->andWhere('l.endAt >= :now')
            ->orWhere('l.returnAt is null')
            ->setParameter('now',$now)
            ->setParameter('user', $user)
            ->orderBy('v.id')
            ->groupBy('v.id');

        return $query->getQuery()
            ->getResult();
    }

    public function findVehiclesLocationIds(\DateTime $now){
        $query = $this->createQueryBuilder('l')
            ->select('v.id')
            ->join('l.vehicle','v')
            ->andWhere('l.startAt <= :now')
            ->andWhere('l.endAt >= :now')
            ->andWhere('l.returnAt is null')
            ->setParameter('now',$now)
            ->orderBy('v.id')
            ->groupBy('v.id');

        return $query->getQuery()
            ->getResult();
    }

    public function findVehiclesLocationIdsOwner(\DateTime $now, User $user){
        $query = $this->createQueryBuilder('l')
            ->select('v.id')
            ->join('l.vehicle','v')
            ->andWhere('v.user = :user')
            ->andWhere('l.startAt <= :now')
            ->andWhere('l.endAt >= :now')
            ->andWhere('l.returnAt is null')
            ->setParameter('now',$now)
            ->setParameter('user', $user)
            ->orderBy('v.id')
            ->groupBy('v.id');

        return $query->getQuery()
            ->getResult();
    }
}
