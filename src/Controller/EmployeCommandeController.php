<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\HistoriqueStatutCommande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mime\Address;

#[Route('/employe/commandes')]
class EmployeCommandeController extends AbstractController
{
    /**
     * Affiche les commandes et permet de les filtrer.
     */
    #[Route('', name: 'app_employe_commande_index', methods: ['GET'])]
    public function index(
        CommandeRepository $commandeRepository,
        Request $request
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        $statut = trim((string) $request->query->get('statut', ''));
        $client = trim((string) $request->query->get('client', ''));

        // Recherche par nom, prénom ou email du client.
        $commandes = $commandeRepository->findByClient($client);

        // Filtre sur le dernier statut de chaque commande.
        if ($statut !== '') {
            $commandes = array_filter(
                $commandes,
                fn (Commande $commande): bool =>
                    $this->getStatutActuel($commande) === $statut
            );
        }

        return $this->render('employe/commande/index.html.twig', [
            'commandes' => $commandes,
            'statutSelectionne' => $statut,
            'clientRecherche' => $client,
        ]);
    }

    /**
     * Affiche le détail d'une commande pour l'employé.
     */
    #[Route(
        '/{id}',
        name: 'app_employe_commande_show',
        methods: ['GET'],
        requirements: ['id' => '\d+']
    )]
    public function show(Commande $commande): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        return $this->render('employe/commande/show.html.twig', [
            'commande' => $commande,
            'statutActuel' => $this->getStatutActuel($commande),
        ]);
    }

    /**
     * Permet à l'employé de modifier une commande.
     * Le contact préalable avec le client reste obligatoire.
     */
    #[Route(
        '/{id}/modifier',
        name: 'app_employe_commande_edit',
        methods: ['GET', 'POST'],
        requirements: ['id' => '\d+']
    )]
    public function edit(
        Commande $commande,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $moyenContact = trim(
                (string) $request->request->get('moyen_contact')
            );

            $motif = trim(
                (string) $request->request->get('motif')
            );

            // L'employé doit contacter le client avant une modification.
            if (
                !in_array($moyenContact, ['email', 'gsm'], true)
                || $motif === ''
            ) {
                $this->addFlash(
                    'danger',
                    'Indiquez le moyen de contact et le motif de la modification.'
                );

                return $this->render(
                    'employe/commande/edit.html.twig',
                    [
                        'commande' => $commande,
                        'form' => $form,
                    ]
                );
            }

            // Le nombre de personnes doit toujours respecter le minimum du menu.
            if (!$this->verifierNombreMinimum($commande)) {
                $this->addFlash(
                    'danger',
                    'Le nombre minimum de personnes n’est pas respecté.'
                );

                return $this->render(
                    'employe/commande/edit.html.twig',
                    [
                        'commande' => $commande,
                        'form' => $form,
                    ]
                );
            }

            /*
             * Le prix est recalculé côté serveur après modification.
             * Une modification ne change pas le stock déjà réservé.
             */
            $this->calculerPrix($commande);

            // Conserve la preuve du contact avec le client.
            $commande->setMoyenContactEmploye($moyenContact);
            $commande->setMotifInterventionEmploye($motif);
            $commande->setDateContactEmploye(new \DateTimeImmutable());

            $entityManager->flush();

            $this->addFlash(
                'success',
                'La commande a été modifiée et le contact client enregistré.'
            );

            return $this->redirectToRoute(
                'app_employe_commande_show',
                ['id' => $commande->getId()]
            );
        }

        return $this->render('employe/commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    /**
     * Annule une commande après contact avec le client.
     * L'unité de stock réservée est alors rendue au menu.
     */
    #[Route(
        '/{id}/annuler',
        name: 'app_employe_commande_cancel',
        methods: ['POST'],
        requirements: ['id' => '\d+']
    )]
    public function cancel(
        Commande $commande,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        // Protège l'annulation contre les requêtes CSRF.
        if (!$this->isCsrfTokenValid(
            'annuler_employe_' . $commande->getId(),
            (string) $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException(
                'Jeton CSRF invalide.'
            );
        }

        /*
         * Une commande déjà annulée ne doit pas être annulée une seconde fois.
         * Cela évite notamment de restituer plusieurs fois la même unité de stock.
         */
        if ($this->getStatutActuel($commande) === 'annulée') {
            $this->addFlash(
                'danger',
                'Cette commande est déjà annulée.'
            );

            return $this->redirectToRoute(
                'app_employe_commande_show',
                ['id' => $commande->getId()]
            );
        }

        $moyenContact = trim(
            (string) $request->request->get('moyen_contact')
        );

        $motif = trim(
            (string) $request->request->get('motif')
        );

        // Une annulation employé nécessite un contact préalable.
        if (
            !in_array($moyenContact, ['email', 'gsm'], true)
            || $motif === ''
        ) {
            $this->addFlash(
                'danger',
                'Le contact du client et le motif sont obligatoires.'
            );

            return $this->redirectToRoute(
                'app_employe_commande_show',
                ['id' => $commande->getId()]
            );
        }

        // Conserve la preuve du contact et le motif de l'annulation.
        $commande->setMoyenContactEmploye($moyenContact);
        $commande->setMotifInterventionEmploye($motif);
        $commande->setDateContactEmploye(new \DateTimeImmutable());

        // L'annulation reste enregistrée dans l'historique de la commande.
        $this->ajouterStatut(
            $commande,
            'annulée',
            $entityManager
        );

        /*
         * Une commande avait consommé une disponibilité lors de sa création.
         * Son annulation rend donc une unité au stock du menu.
         */
        $menu = $commande->getMenu();

        if ($menu !== null) {
            $menu->setStock(($menu->getStock() ?? 0) + 1);
        }

        // Doctrine enregistre ensemble l'annulation, le contact et le stock.
        $entityManager->flush();

        $this->addFlash(
            'success',
            'La commande a été annulée.'
        );

        return $this->redirectToRoute(
            'app_employe_commande_show',
            ['id' => $commande->getId()]
        );
    }

    /**
     * Modifie le statut d'une commande et conserve son historique.
     */
    #[Route(
        '/{id}/statut',
        name: 'app_employe_commande_statut',
        methods: ['POST'],
        requirements: ['id' => '\d+']
    )]
    public function changerStatut(
        Commande $commande,
        Request $request,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        // Protège le changement de statut contre les requêtes CSRF.
        if (!$this->isCsrfTokenValid(
            'statut_commande_' . $commande->getId(),
            (string) $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException(
                'Jeton CSRF invalide.'
            );
        }

        $nouveauStatut = (string) $request->request->get('statut');

        // Seuls les statuts prévus par le parcours métier sont autorisés.
        $statutsAutorises = [
            'acceptée',
            'en préparation',
            'en cours de livraison',
            'livrée',
            'en attente du retour de matériel',
            'terminée',
        ];

        if (!in_array($nouveauStatut, $statutsAutorises, true)) {
            $this->addFlash('danger', 'Statut invalide.');

            return $this->redirectToRoute(
                'app_employe_commande_show',
                ['id' => $commande->getId()]
            );
        }

        // Une commande annulée ne doit plus reprendre son parcours normal.
        if ($this->getStatutActuel($commande) === 'annulée') {
            $this->addFlash(
                'danger',
                'Une commande annulée ne peut plus changer de statut.'
            );

            return $this->redirectToRoute(
                'app_employe_commande_show',
                ['id' => $commande->getId()]
            );
        }

        // Évite d'ajouter deux fois de suite le même statut à l'historique.
        if ($this->getStatutActuel($commande) === $nouveauStatut) {
            $this->addFlash(
                'danger',
                'La commande possède déjà ce statut.'
            );

            return $this->redirectToRoute(
                'app_employe_commande_show',
                ['id' => $commande->getId()]
            );
        }

        // Le matériel prêté est renseigné depuis la fiche employé.
        $commande->setMaterielPrete(
            $request->request->get('materiel_prete') === '1'
        );

        $this->ajouterStatut(
            $commande,
            $nouveauStatut,
            $entityManager
        );

        $entityManager->flush();

        // Le client est averti du délai de restitution du matériel.
        if ($nouveauStatut === 'en attente du retour de matériel') {
            $email = (new Email())
                ->from(new Address('hedi94220@gmail.com', 'Vite & Gourmand'))
                ->to($commande->getUtilisateur()->getEmail())
                ->subject('Vite & Gourmand - Retour du matériel')
                ->text(
                    "Du matériel doit être retourné pour votre commande n°"
                    . $commande->getId()
                    . ". Merci de le restituer sous 10 jours ouvrés. "
                    . "Au-delà de ce délai, des frais de 600 € peuvent être appliqués."
                );

            $mailer->send($email);
        }

        // Une commande terminée permet ensuite au client de donner son avis.
        if ($nouveauStatut === 'terminée') {
            $email = (new Email())
                ->from(new Address('hedi94220@gmail.com', 'Vite & Gourmand'))
                ->to($commande->getUtilisateur()->getEmail())
                ->subject('Vite & Gourmand - Donnez votre avis')
                ->text(
                    "Votre commande n°"
                    . $commande->getId()
                    . " est terminée. Vous pouvez maintenant vous connecter "
                    . "à votre espace client pour donner votre avis."
                );

            $mailer->send($email);
        }

        $this->addFlash(
            'success',
            'Le statut de la commande a été mis à jour.'
        );

        return $this->redirectToRoute(
            'app_employe_commande_show',
            ['id' => $commande->getId()]
        );
    }

    /**
     * Ajoute un nouveau statut daté sans supprimer l'historique précédent.
     */
    private function ajouterStatut(
        Commande $commande,
        string $statut,
        EntityManagerInterface $entityManager
    ): void {
        $historique = new HistoriqueStatutCommande();
        $historique->setCommande($commande);
        $historique->setStatut($statut);
        $historique->setDateModification(new \DateTimeImmutable());

        $entityManager->persist($historique);
    }

    /**
     * Retourne le dernier statut enregistré pour la commande.
     */
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

    /**
     * Vérifie que le nombre de personnes respecte le minimum du menu.
     */
    private function verifierNombreMinimum(Commande $commande): bool
    {
        $menu = $commande->getMenu();

        return $menu !== null
            && $commande->getNombrePersonnes() !== null
            && $commande->getNombrePersonnes()
                >= $menu->getNombreMinimumPersonnes();
    }

    /**
     * Recalcule le prix définitif côté serveur.
     *
     * Le même calcul est utilisé que pour la création côté client :
     * prix selon le nombre de personnes, réduction et frais de livraison.
     */
    private function calculerPrix(Commande $commande): void
    {
        $menu = $commande->getMenu();

        // Une commande doit toujours être liée à un menu.
        if ($menu === null) {
            throw new \LogicException(
                'Impossible de calculer le prix sans menu.'
            );
        }

        $nombrePersonnes = $commande->getNombrePersonnes() ?? 0;
        $nombreMinimum = $menu->getNombreMinimumPersonnes() ?? 0;
        $prixMinimum = (float) $menu->getPrixMinimum();

        // Empêche une division par zéro en cas de donnée incorrecte.
        if ($nombreMinimum <= 0) {
            throw new \LogicException(
                'Le nombre minimum de personnes du menu doit être supérieur à zéro.'
            );
        }

        // Le prix minimum correspond au nombre minimum de personnes.
        $prixParPersonne = $prixMinimum / $nombreMinimum;
        $prixMenu = $prixParPersonne * $nombrePersonnes;

        $reduction = 0.0;

        // Applique 10 % à partir de 5 personnes au-dessus du minimum.
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

        // Doctrine attend des chaînes pour les colonnes DECIMAL.
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
}