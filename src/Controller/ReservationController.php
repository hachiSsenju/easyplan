<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use App\Repository\SalleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'app_reservation')]
    public function index(ReservationRepository $reservations,UserRepository $userRepository): Response
    {
         $user = $this->getUser()->getUserIdentifier();
           $userEntity = $userRepository->findOneBy(['email' => $user]);
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservations->findAll(),
            'currentUser' => $userEntity,
        ]);
    }
    #[Route('/reservation/add', name: 'add_reservation')]
public function add(
    SalleRepository $salles,
    UserRepository $userRepository,
    ReservationRepository $reservationRepository,
    EntityManagerInterface $entityManager
): Response {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = $this->getUser()->getUserIdentifier();
        $userEntity = $userRepository->findOneBy(['email' => $user]);

        $salleId = intval($_POST['salle']);
        $salle = $salles->find($salleId);

        if ($salle && $userEntity) {
            $date = new \DateTime($_POST['date']);
            $heureD = new \DateTime($_POST['date'] . ' ' . $_POST['heureD']);
            $heureF = new \DateTime($_POST['date'] . ' ' . $_POST['heureF']);

            $conflit = $reservationRepository->createQueryBuilder('r')
                ->where('r.Salle = :salle')
                ->andWhere('r.date = :date')
                ->andWhere('r.heureD < :heureF')
                ->andWhere('r.heureF > :heureD')
               ->setParameter('salle', $salle)
                ->setParameter('date', $date)
                ->setParameter('heureD', $heureD)
                ->setParameter('heureF', $heureF)
                ->getQuery()
                ->getResult();

            if ($conflit) {
                $message = 'La salle est déjà réservée pour cet horaire. Horaires déjà réservés: ';
    
                foreach ($conflit as $reservation) {
                    $message .= sprintf(
                        'de %s à %s, ',
                        $reservation->getHeureD()->format('H:i'),
                        $reservation->getHeureF()->format('H:i')
                    );
                }
    
   
                $message = rtrim($message, ', ');
            
                $this->addFlash('error', $message);
                        return $this->redirectToRoute('add_reservation');
                    }

                    // Aucune collision, créer la réservation
                    $reservation = new Reservation();
                    $reservation->setSalle($salle);
                    $userEntity->addReservation($reservation);
                    $reservation->setDate($date);
                    $reservation->setHeureD($heureD);
                    $reservation->setHeureF($heureF);

                    $entityManager->persist($reservation);
                    $entityManager->flush();

                    $this->addFlash('success', 'Réservation ajoutée avec succès !');
                    return $this->redirectToRoute('app_reservation');
                }
    }

    return $this->render('reservation/add.html.twig', [
        'salles' => $salles->findAll(),
    ]);
}

}
