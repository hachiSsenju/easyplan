<?php

namespace App\Controller;

use App\Repository\ReservationRepository;
use App\Repository\SalleRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
   public function index(
    SalleRepository $salleRepository,
    UserRepository $userRepository,
    ReservationRepository $reservationRepository
): Response {
    $totalSalles = $salleRepository->count([]);
    $totalUsers = $userRepository->count([]);
    $totalReservations = $reservationRepository->count([]);

    return $this->render('home/index.html.twig', [
        'totalSalles' => $totalSalles,
        'totalUsers' => $totalUsers,
        'totalReservations' => $totalReservations,
        'user' => $this->getUser()
    ]);
}

}
