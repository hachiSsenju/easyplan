<?php

namespace App\Controller;

use App\Entity\Salle;

use App\Repository\SalleRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SalleController extends AbstractController
{
    #[Route('/salle', name: 'app_salle')]
    public function index(SalleRepository $salle_repository): Response
    {
      $dispo = [];
$salles = $salle_repository->findAll();
$now = new \DateTime(); // maintenant

foreach ($salles as $salle) {
    $reservations = $salle->getReservations();
    $isAvailable = true;

    foreach ($reservations as $reservation) {
        $dateRes = $reservation->getDate();
        $heureDebut = $reservation->getHeureD();
        $heureFin = $reservation->getHeureF();

        // Vérifie que la date est aujourd'hui
        if ($dateRes->format('Y-m-d') === $now->format('Y-m-d')) {
            // Fusionne date + heure pour comparer avec maintenant
            $start = (clone $dateRes)->setTime(
                (int)$heureDebut->format('H'),
                (int)$heureDebut->format('i')
            );
            $end = (clone $dateRes)->setTime(
                (int)$heureFin->format('H'),
                (int)$heureFin->format('i')
            );

            if ($now >= $start && $now <= $end) {
                $isAvailable = false;
                break;
            }
        }
    }

    if ($isAvailable) {
        $dispo[] = $salle;
    }
}

return $this->render('salle/index.html.twig', [
    'salles' => $salles,
    'dispo' => $dispo,
    'showForm' => 'none'
]);

    }
    #[Route('/salle/edit/{id}', name: 'edit_salle')]
    public function edit(SalleRepository $salle_repository,int $id, EntityManagerInterface $entity): Response
    {
        $idS= intval($id);
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $salle = $salle_repository->find($idS);
            $salle->setNom($_POST['nom']);
            $salle->setCapacite($_POST['capacite']);
            $entity->persist($salle);
            $entity->flush();

            return $this->redirectToRoute('app_salle');
        }
        // $salle = $salle_repository->find($idS);
        return $this->render('salle/index.html.twig', [
            'salle' => $salle_repository->find($idS),
            'salles' => $salle_repository->findAll(),
            'showForm' => 'block'
        ]);
    }
    #[Route('salle/delete/{id}', name: 'delete_salle')]
    public function delete(int $id,SalleRepository $salle_repository,EntityManagerInterface $entityManager): Response
    {
        $idS = intval($id);
        $salle = $salle_repository->find($idS);
        if ($salle) {
            $entityManager->remove($salle);
            $entityManager->flush();
            return $this->redirectToRoute('app_salle');
        }
        return $this->render('salle/index.html.twig', [
            'salles' => $salle_repository->findAll(),
            'showForm' => 'none'
        ]);
    }
    #[Route('/salle/add', name: 'add_salle')]
    public function add(EntityManagerInterface $entity): Response
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $salle = new Salle();
            // $salle->setNom($_POST['nom']);
            // $salle->setCapacite($_POST['capacite']);
            extract($_POST);
            $salle->setNom($nom);
            $salle->setCapacite($capacite);
            
            $entity->persist($salle);
            $entity->flush();

            return $this->redirectToRoute('app_salle');
        }

        return $this->render('salle/add.html.twig', [
            // 'salles' => $salle_repository->findAll(),
            'showForm' => 'none'
        ]);
    }
}