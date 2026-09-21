<?php

namespace App\Controller;

use App\Entity\Horaire;
use App\Form\HoraireType;
use App\Repository\HoraireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/employe/horaires')]
#[IsGranted('ROLE_EMPLOYE')]
class HoraireController extends AbstractController
{
    #[Route('', name: 'app_horaire_index', methods: ['GET'])]
    public function index(
        HoraireRepository $horaireRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Crée les 7 jours uniquement s'ils n'existent pas encore.
        // Cela évite de recharger les fixtures et de supprimer les données actuelles.
        $this->creerHorairesManquants($horaireRepository, $entityManager);

        return $this->render('horaire/index.html.twig', [
            'horaires' => $this->trierHoraires($horaireRepository->findAll()),
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_horaire_edit', methods: ['GET', 'POST'])]
    public function edit(
        Horaire $horaire,
        \Symfony\Component\HttpFoundation\Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(HoraireType::class, $horaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Les horaires ont été modifiés.');

            return $this->redirectToRoute('app_horaire_index');
        }

        return $this->render('horaire/edit.html.twig', [
            'horaire' => $horaire,
            'form' => $form,
        ]);
    }

    /**
     * Ajoute les jours manquants avec des heures de démonstration.
     * L'employé pourra ensuite définir les horaires réels.
     */
    private function creerHorairesManquants(
        HoraireRepository $horaireRepository,
        EntityManagerInterface $entityManager
    ): void {
        $jours = [
            'Lundi',
            'Mardi',
            'Mercredi',
            'Jeudi',
            'Vendredi',
            'Samedi',
            'Dimanche',
        ];

        $modification = false;

        foreach ($jours as $jour) {
            if ($horaireRepository->findOneBy(['jour' => $jour])) {
                continue;
            }

            $horaire = new Horaire();
            $horaire->setJour($jour);
            $horaire->setHeureOuverture(new \DateTimeImmutable('09:00'));
            $horaire->setHeureFermeture(new \DateTimeImmutable('18:00'));

            $entityManager->persist($horaire);
            $modification = true;
        }

        if ($modification) {
            $entityManager->flush();
        }
    }

    /**
     * Conserve toujours l'ordre lundi -> dimanche à l'écran.
     */
    private function trierHoraires(array $horaires): array
    {
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

        return $horaires;
    }
}