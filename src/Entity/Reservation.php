<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?Salle $Salle = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $heureD = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $heureF = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $utilisateur = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Salle $salle = null;


   

   
 
       public function getId(): ?int
    {
        return $this->id;
    }

    public function getSalle(): ?Salle
    {
        return $this->salle;
    }

    public function setSalle(?Salle $Salle): static
    {
        $this->salle = $Salle;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getHeureD(): ?\DateTime
    {
        return $this->heureD;
    }

    public function setHeureD(\DateTime $heureD): static
    {
        $this->heureD = $heureD;

        return $this;
    }

    public function getHeureF(): ?\DateTime
    {
        return $this->heureF;
    }

    public function setHeureF(\DateTime $heureF): static
    {
        $this->heureF = $heureF;

        return $this;
    }

    /**
     * @return Collection<int, USer>
     */

    /**
     * @return Collection<int, User>
     */

    public function getUtilisateur(): ?User
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?User $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }



  
   
 

  

   
}
