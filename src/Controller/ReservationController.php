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
    public function add(SalleRepository $salles,UserRepository $userRepository,EntityManagerInterface $entityManager): Response
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
           $user = $this->getUser()->getUserIdentifier();
           $userEntity = $userRepository->findOneBy(['email' => $user]);
              $salleId = intval($_POST['salle']);
              $salle = $salles->find($salleId);
              if ($salle && $userEntity) {
                $reservation = new Reservation();
                $reservation->setSalle($salle);
               $userEntity->addReservation($reservation);
                $reservation->setDate(new \DateTime($_POST['date']));
                $reservation->setHeureD(new \DateTime($_POST['heureD']));
                $reservation->setHeureF(new \DateTime($_POST['heureF']));
                $entityManager->persist($reservation);
                $entityManager->flush();
              }
    
                return $this->redirectToRoute('app_reservation');
              
        }

        return $this->render('reservation/add.html.twig', [
            'salles' => $salles->findAll(),
        ]);
    }
}
