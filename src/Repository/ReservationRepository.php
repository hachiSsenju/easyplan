<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

//    /**
//     * @return Reservation[] Returns an array of Reservation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Reservation
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
public function findReservationsGroupedByDate(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
{
    $qb = $this->createQueryBuilder('r')
        ->join('r.salle', 's')
        ->join('r.utilisateur', 'u')
        ->select('r.date', 'r.heureD AS heureDebut', 'r.heureF AS heureFin', 's.nom AS salle', 'u.email AS utilisateur')
        ->where('r.date BETWEEN :start AND :end')
        ->setParameter('start', $startDate)
        ->setParameter('end', $endDate)
        ->orderBy('r.date', 'ASC')
        ->addOrderBy('s.nom', 'ASC');

    return $qb->getQuery()->getResult();
}

}
