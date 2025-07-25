<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class DisponibiliteSalleValidator extends ConstraintValidator
{
    // public function validate(mixed $value, Constraint $constraint): void
    // {
        // /** @var DisponibiliteSalle $constraint */

        // if (null === $value || '' === $value) {
        //     return;
        // }

        // // TODO: implement the validation here
        // $this->context->buildViolation($constraint->message)
        //     ->setParameter('{{ value }}', $value)
        //     ->addViolation()
        // ;

        public function validate($reservation, Constraint $constraint)
{
    $salle = $reservation->getSalle();
    $debut = $reservation->getHeureDebut();
    $fin = $reservation->getHeureFin();

    $reservationsExistantes = $this->entityManager
        ->getRepository(Reservation::class)
        ->findChevauchements($salle, $debut, $fin);

    if (count($reservationsExistantes) > 0) {
        $this->context->buildViolation($constraint->message)
            ->atPath('heureDebut')
            ->addViolation();
    }
}
    
}
