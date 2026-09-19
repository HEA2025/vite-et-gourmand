<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\HistoriqueStatutCommande;
use App\Entity\Menu;
use App\Entity\Utilisateur;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/commande')]
class CommandeController extends AbstractController
{
    /**
     * Crée une nouvelle commande à partir du menu sélectionné.
     * Le prix définitif est toujours recalculé côté serveur.
     */
    #[Route(
        '/nouvelle/{id}',
        name: 'app_commande_new',
        methods: ['GET', 'POST'],
        requirements: ['id' => '\d+']
    )]
    public function new(
        Menu $menu,
        Request $request,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();

        $commande = new Commande();

        // Le menu provient de la page détaillée et n'est pas choisi dans le formulaire.
        $commande->setMenu($menu);
        $commande->setUtilisateur($utilisateur);
        $commande->setDateCreation(new \DateTimeImmutable());

        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Refuse une commande inférieure au minimum prévu par le menu.
            if (!$this->verifierNombreMinimum($commande)) {
                $this->addFlash(
                    'danger',
                    'Ce menu nécessite au minimum '
                    . $menu->getNombreMinimumPersonnes()
                    . ' personnes.'
                );

                return $this->render('commande/new.html.twig', [
                    'form' => $form,
                    'menu' => $menu,
                ]);
            }

            // Le calcul JavaScript n'est qu'un aperçu.
            // Le vrai montant enregistré est calculé ici côté serveur.
            $this->calculerPrix($commande);

            // Toute nouvelle commande commence avec le statut "en attente".
            $historique = new HistoriqueStatutCommande();
            $historique->setCommande($commande);
            $historique->setStatut('en attente');
            $historique->setDateModification(new \DateTimeImmutable());

            $entityManager->persist($commande);
            $entityManager->persist($historique);
            $entityManager->flush();

            // Envoie une confirmation après l'enregistrement de la commande.
            $email = (new Email())
                ->from('no-reply@vite-et-gourmand.fr')
                ->to($utilisateur->getEmail())
                ->subject('Vite & Gourmand - Confirmation de commande')
                ->text(
                    "Votre commande n°"
                    . $commande->getId()
                    . " a bien été enregistrée.\n\n"
                    . "Menu : "
                    . $menu->getTitre()
                    . "\n"
                    . "Nombre de personnes : "
                    . $commande->getNombrePersonnes()
                    . "\n"
                    . "Montant total : "
                    . $commande->getPrixTotal()
                    . " €."
                );

            $mailer->send($email);

            $this->addFlash(
                'success',
                'Votre commande a bien été enregistrée. Total : '
                . number_format(
                    (float) $commande->getPrixTotal(),
                    2,
                    ',',
                    ' '
                )
                . ' €'
            );

            return $this->redirectToRoute('app_commande_index');
        }

        return $this->render('commande/new.html.twig', [
            'form' => $form,
            'menu' => $menu,
        ]);
    }

    /**
     * Affiche uniquement les commandes du client connecté.
     */
    #[Route(
        '/mes-commandes',
        name: 'app_commande_index',
        methods: ['GET']
    )]
    public function index(
        CommandeRepository $commandeRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();

        $commandes = $commandeRepository->findBy(
            ['utilisateur' => $utilisateur],
            ['dateCreation' => 'DESC']
        );

        return $this->render('commande/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    /**
     * Affiche le détail et l'historique d'une commande.
     */
    #[Route(
        '/{id}',
        name: 'app_commande_show',
        methods: ['GET'],
        requirements: ['id' => '\d+']
    )]
    public function show(Commande $commande): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Empêche un client de consulter la commande d'un autre client.
        $this->verifierProprietaire($commande);

        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
            'statutActuel' => $this->getStatutActuel($commande),
            'modifiable' => $this->estModifiable($commande),
        ]);
    }

    /**
     * Permet au client de modifier sa commande avant son acceptation.
     * Le menu sélectionné reste inchangé.
     */
    #[Route(
        '/{id}/modifier',
        name: 'app_commande_edit',
        methods: ['GET', 'POST'],
        requirements: ['id' => '\d+']
    )]
    public function edit(
        Commande $commande,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Vérifie que la commande appartient au client connecté.
        $this->verifierProprietaire($commande);

        if (!$this->estModifiable($commande)) {
            $this->addFlash(
                'danger',
                'Cette commande ne peut plus être modifiée car elle a déjà été acceptée.'
            );

            return $this->redirectToRoute(
                'app_commande_show',
                ['id' => $commande->getId()]
            );
        }

        // CommandeType ne contient pas le menu : le client ne peut donc pas le changer.
        $menu = $commande->getMenu();

        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->verifierNombreMinimum($commande)) {
                $this->addFlash(
                    'danger',
                    'Ce menu nécessite au minimum '
                    . $menu->getNombreMinimumPersonnes()
                    . ' personnes.'
                );

                return $this->render('commande/edit.html.twig', [
                    'form' => $form,
                    'commande' => $commande,
                ]);
            }

            // Recalcule les montants après une modification.
            $this->calculerPrix($commande);

            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre commande a bien été modifiée.'
            );

            return $this->redirectToRoute(
                'app_commande_show',
                ['id' => $commande->getId()]
            );
        }

        return $this->render('commande/edit.html.twig', [
            'form' => $form,
            'commande' => $commande,
        ]);
    }

    /**
     * Annule une commande tant qu'elle n'a pas encore été acceptée.
     * La commande est conservée afin de garder une trace dans l'historique.
     */
    #[Route(
        '/{id}/annuler',
        name: 'app_commande_delete',
        methods: ['POST'],
        requirements: ['id' => '\d+']
    )]
    public function delete(
        Commande $commande,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Empêche l'annulation de la commande d'un autre client.
        $this->verifierProprietaire($commande);

        if (!$this->estModifiable($commande)) {
            $this->addFlash(
                'danger',
                'Cette commande ne peut plus être annulée car elle a déjà été acceptée.'
            );

            return $this->redirectToRoute(
                'app_commande_show',
                ['id' => $commande->getId()]
            );
        }

        // Protège l'action d'annulation contre les requêtes CSRF.
        if (!$this->isCsrfTokenValid(
            'annuler_commande_' . $commande->getId(),
            (string) $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException(
                'Jeton CSRF invalide.'
            );
        }

        // On conserve la commande et on ajoute l'annulation à son historique.
        $historique = new HistoriqueStatutCommande();
        $historique->setCommande($commande);
        $historique->setStatut('annulée');
        $historique->setDateModification(new \DateTimeImmutable());

        $entityManager->persist($historique);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Votre commande a été annulée.'
        );

        return $this->redirectToRoute('app_commande_index');
    }

    /**
     * Vérifie que le client connecté possède bien la commande.
     */
    private function verifierProprietaire(Commande $commande): void
    {
        if ($commande->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException(
                'Vous ne pouvez pas accéder à cette commande.'
            );
        }
    }

    /**
     * Vérifie le nombre minimum de personnes imposé par le menu.
     */
    private function verifierNombreMinimum(Commande $commande): bool
    {
        $menu = $commande->getMenu();

        return $commande->getNombrePersonnes()
            >= $menu->getNombreMinimumPersonnes();
    }

    /**
     * Calcule les montants définitifs enregistrés en base.
     *
     * Réduction :
     * 10 % à partir de 5 personnes supplémentaires par rapport au minimum.
     *
     * Livraison :
     * gratuite à Bordeaux, sinon 5 € + 0,59 € par kilomètre.
     */
    private function calculerPrix(Commande $commande): void
    {
        $menu = $commande->getMenu();

        $nombrePersonnes = $commande->getNombrePersonnes();
        $nombreMinimum = $menu->getNombreMinimumPersonnes();

        $prixMenu = (float) $menu->getPrixMinimum();

        $reduction = 0.0;

        // Applique la réduction prévue à partir du seuil demandé.
        if ($nombrePersonnes >= ($nombreMinimum + 5)) {
            $reduction = $prixMenu * 0.10;
        }

        $distanceLivraison = max(
            0.0,
            $commande->getDistanceLivraison() ?? 0.0
        );

        // Une distance de 0 représente une livraison dans Bordeaux.
        if ($distanceLivraison === 0.0) {
            $fraisLivraison = 0.0;
        } else {
            $fraisLivraison = 5 + (0.59 * $distanceLivraison);
        }

        $prixTotal = $prixMenu - $reduction + $fraisLivraison;

        $commande->setDistanceLivraison($distanceLivraison);

        $commande->setPrixMenu(
            number_format($prixMenu, 2, '.', '')
        );

        $commande->setReduction(
            number_format($reduction, 2, '.', '')
        );

        $commande->setFraisLivraison(
            number_format($fraisLivraison, 2, '.', '')
        );

        $commande->setPrixTotal(
            number_format($prixTotal, 2, '.', '')
        );
    }

    /**
     * Retourne le dernier statut enregistré pour la commande.
     */
    private function getStatutActuel(Commande $commande): string
    {
        $dernierHistorique = null;

        foreach (
            $commande->getHistoriqueStatutCommandes()
            as $historique
        ) {
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

    /**
     * Une commande client est modifiable uniquement avant son acceptation.
     */
    private function estModifiable(Commande $commande): bool
    {
        return $this->getStatutActuel($commande) === 'en attente';
    }
}