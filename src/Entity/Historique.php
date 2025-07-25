<?php

namespace App\Entity;

use App\Repository\HistoriqueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoriqueRepository::class)]
class Historique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private array $reservation = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReservation(): array
    {
        return $this->reservation;
    }

    public function setReservation(array $reservation): static
    {
        $this->reservation = $reservation;

        return $this;
    }
}
