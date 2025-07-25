<?php

namespace App\Controller;

use App\Entity\Historique;
use App\Entity\History;
use App\Entity\Reservation;
use App\Repository\HistoriqueRepository;
use App\Repository\ReservationRepository;
use App\Repository\SalleRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'app_reservation')]
    public function index(ReservationRepository $reservations, UserRepository $userRepository): Response
    {
        $user = $this->getUser()->getUserIdentifier();
        $userEntity = $userRepository->findOneBy(['email' => $user]);
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservations->findAll(),
            'currentUser' => $userEntity,
        ]);
    }
    #[Route('/reservation/delete/{id}', name: 'delete_reservation')]
    public function delete(ReservationRepository $reservations, EntityManagerInterface $entityManager, int $id, UserRepository $userRepository): Response
    {
        $user = $this->getUser()->getUserIdentifier();
        $userEntity = $userRepository->findOneBy(['email' => $user]);
        $idR = intval($id);
        $reservation = $reservations->find($idR);
        if ($reservation) {
            $historique = new Historique();
            $deletedReservation = [
                'salle' => $reservation->getSalle()->getNom(),
                'date' => $reservation->getDate()->format('Y-m-d'),
                'heureD' => $reservation->getHeureD()->format('H:i'),
                'heureF' => $reservation->getHeureF()->format('H:i'),
                'user' => $reservation->getUtilisateur()->getEmail(),
                'status' => 'deleted'
            ];
            $historique->setReservation([$deletedReservation]);
            $entityManager->persist($historique);
            $entityManager->remove($reservation);
            $entityManager->flush();
            $this->addFlash('success', 'Réservation supprimée avec succès !');
        }
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservations->findAll(),
            'currentUser' => $userEntity,
        ]);
    }
    #[Route('/reservation/history', name: 'history_reservation')]
    public function history(HistoriqueRepository $history, UserRepository $userRepository): Response
    {
        $user = $this->getUser()->getUserIdentifier();
        $userEntity = $userRepository->findOneBy(['email' => $user]);
        return $this->render('reservation/history.html.twig', [
            'histories' => $history->findAll(),
            // 'currentUser' => $userEntity,
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

                if ($reservation->getHeureF() < $reservation->getHeureD()) {
                    $this->addFlash('error', 'L\'heure de fin doit être postérieure à l\'heure de début.');
                    return $this->redirectToRoute('add_reservation');
                }
                if ($reservation->getHeureD() < new \DateTime()) {
                    $this->addFlash('error', 'L\'heure de début ne peut pas être dans le passé.');
                    return $this->redirectToRoute('add_reservation');
                }
                if ($reservation->getDate() < new \DateTime()) {
                    $this->addFlash('error', 'La date de réservation ne peut pas être dans le passé.');
                    return $this->redirectToRoute('add_reservation');
                }


                $entityManager->persist($reservation);
                $historique = new Historique();
                $newReservation = [
                    'salle' => $salle->getNom(),
                    'date' => $reservation->getDate()->format('Y-m-d'),
                    'heureD' => $reservation->getHeureD()->format('H:i'),
                    'heureF' => $reservation->getHeureF()->format('H:i'),
                    'user' => $userEntity->getEmail(),
                    'status' => 'created'
                ];
                $historique->setReservation([$newReservation]);
                $entityManager->persist($historique);
                $entityManager->flush();

                $this->addFlash('success', 'Réservation ajoutée avec succès !');
                return $this->redirectToRoute('app_reservation');
            }
        }

        return $this->render('reservation/add.html.twig', [
            'salles' => $salles->findAll(),
        ]);
    }






    #[Route('/reservation/edit/{id}', name: 'add_reservation')]
    public function edit(
        SalleRepository $salles,
        UserRepository $userRepository,
        ReservationRepository $reservationRepository,
        EntityManagerInterface $entityManager,
        int $id
    ): Response {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $this->getUser()->getUserIdentifier();
            $userEntity = $userRepository->findOneBy(['email' => $user]);

            $salleId = intval($id);
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

                if ($reservation->getHeureF() < $reservation->getHeureD()) {
                    $this->addFlash('error', 'L\'heure de fin doit être postérieure à l\'heure de début.');
                    return $this->redirectToRoute('add_reservation');
                }
                if ($reservation->getHeureD() < new \DateTime()) {
                    $this->addFlash('error', 'L\'heure de début ne peut pas être dans le passé.');
                    return $this->redirectToRoute('add_reservation');
                }
                if ($reservation->getDate() < new \DateTime()) {
                    $this->addFlash('error', 'La date de réservation ne peut pas être dans le passé.');
                    return $this->redirectToRoute('add_reservation');
                }


                $entityManager->persist($reservation);
                $historique = new Historique();
                $newReservation = [
                    'salle' => $salle->getNom(),
                    'date' => $reservation->getDate()->format('Y-m-d'),
                    'heureD' => $reservation->getHeureD()->format('H:i'),
                    'heureF' => $reservation->getHeureF()->format('H:i'),
                    'user' => $userEntity->getEmail(),
                    'status' => 'created'
                ];
                $historique->setReservation([$newReservation]);
                $entityManager->persist($historique);
                $entityManager->flush();

                $this->addFlash('success', 'Réservation ajoutée avec succès !');
                return $this->redirectToRoute('app_reservation');
            }
        }

        return $this->render('reservation/edit.html.twig', [
            'salle' => $salles->find($salleId),
        ]);
    }
}
