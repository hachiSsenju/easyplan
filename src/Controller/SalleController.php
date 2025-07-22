<?php

namespace App\Controller;

use App\Entity\Salle;

use App\Repository\SalleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SalleController extends AbstractController
{
    #[Route('/salle', name: 'app_salle')]
    public function index(SalleRepository $salle_repository): Response
    {
        return $this->render('salle/index.html.twig', [
            'salles' => $salle_repository->findAll(),
        ]);
    }
    #[Route('/salle/edit/{id}', name: 'adit_salle')]
    public function edit(SalleRepository $salle_repository): Response
    {
        return $this->render('salle/index.html.twig', [
            'salles' => $salle_repository->findAll(),
        ]);
    }
    #[Route('salle/delete/{id}', name: 'adit_salle')]
    public function delete(SalleRepository $salle_repository): Response
    {
        return $this->render('salle/index.html.twig', [
            'salles' => $salle_repository->findAll(),
        ]);
    }
    #[Route('/salle/add', name: 'add_salle')]
    public function add(EntityManagerInterface $entity): Response
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $salle = new Salle();
            $salle->setNom($_POST['nom']);
            $salle->setCapacite($_POST['capacite']);
            $entity->persist($salle);
            $entity->flush();

            return $this->redirectToRoute('app_salle');
        }

        return $this->render('salle/add.html.twig', [
            // 'salles' => $salle_repository->findAll(),
        ]);
    }
}