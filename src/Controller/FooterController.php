<?php

namespace App\Controller;

use App\Entity\Horaire;
use App\Repository\HoraireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class FooterController extends AbstractController
{
    /**
     * Récupère les horaires pour les afficher dans le pied de page.
     */
    public function horaires(HoraireRepository $horaireRepository): Response
    {
        $horaires = $horaireRepository->findAll();

        // Conserve l'ordre naturel de la semaine dans le footer.
        $ordre = [
            'Lundi' => 1,
            'Mardi' => 2,
            'Mercredi' => 3,
            'Jeudi' => 4,
            'Vendredi' => 5,
            'Samedi' => 6,
            'Dimanche' => 7,
        ];

        usort(
            $horaires,
            fn (Horaire $a, Horaire $b): int =>
                ($ordre[$a->getJour()] ?? 99) <=> ($ordre[$b->getJour()] ?? 99)
        );

        return $this->render('footer/_horaires.html.twig', [
            'horaires' => $horaires,
        ]);
    }
}