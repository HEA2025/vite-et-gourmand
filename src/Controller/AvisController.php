<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Commande;
use App\Form\AvisType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/avis')]
class AvisController extends AbstractController
{
    #[Route(
        '/commande/{id}',
        name: 'app_avis_new',
        methods: ['GET', 'POST']
    )]
    public function new(
        Commande $commande,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Vérifie que la commande appartient au client connecté.
        if ($commande->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException(
                'Vous ne pouvez pas donner un avis sur cette commande.'
            );
        }

        // Un avis n'est possible qu'après la fin de la commande.
        if ($this->getStatutActuel($commande) !== 'terminée') {
            $this->addFlash(
                'danger',
                'Vous pourrez donner votre avis lorsque la commande sera terminée.'
            );

            return $this->redirectToRoute(
                'app_commande_show',
                ['id' => $commande->getId()]
            );
        }

        // Une commande ne peut recevoir qu'un seul avis.
        if ($commande->getAvis() !== null) {
            $this->addFlash(
                'danger',
                'Un avis existe déjà pour cette commande.'
            );

            return $this->redirectToRoute(
                'app_commande_show',
                ['id' => $commande->getId()]
            );
        }

        $avis = new Avis();
        $avis->setCommande($commande);
        $avis->setStatut('en attente');
        $avis->setDateCreation(new \DateTimeImmutable());

        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sécurité supplémentaire côté serveur.
            if ($avis->getNote() < 1 || $avis->getNote() > 5) {
                $this->addFlash('danger', 'La note doit être comprise entre 1 et 5.');

                return $this->render('avis/new.html.twig', [
                    'form' => $form,
                    'commande' => $commande,
                ]);
            }

            $entityManager->persist($avis);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre avis a été envoyé et attend la validation d’un employé.'
            );

            return $this->redirectToRoute(
                'app_commande_show',
                ['id' => $commande->getId()]
            );
        }

        return $this->render('avis/new.html.twig', [
            'form' => $form,
            'commande' => $commande,
        ]);
    }

    // Retourne le dernier statut enregistré dans l'historique.
    private function getStatutActuel(Commande $commande): string
    {
        $dernierHistorique = null;

        foreach ($commande->getHistoriqueStatutCommandes() as $historique) {
            if (
                $dernierHistorique === null
                || $historique->getDateModification()
                    > $dernierHistorique->getDateModification()
            ) {
                $dernierHistorique = $historique;
            }
        }

        return $dernierHistorique?->getStatut() ?? 'en attente';
    }
}