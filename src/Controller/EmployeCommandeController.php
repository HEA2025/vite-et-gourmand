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

#[Route('/employe/commandes')]
class EmployeCommandeController extends AbstractController
{
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

        if (!$this->isCsrfTokenValid(
            'annuler_employe_' . $commande->getId(),
            (string) $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException(
                'Jeton CSRF invalide.'
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

        $commande->setMoyenContactEmploye($moyenContact);
        $commande->setMotifInterventionEmploye($motif);
        $commande->setDateContactEmploye(new \DateTimeImmutable());

        // L'annulation est conservée dans l'historique.
        $this->ajouterStatut(
            $commande,
            'annulée',
            $entityManager
        );

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

        if (!$this->isCsrfTokenValid(
            'statut_commande_' . $commande->getId(),
            (string) $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException(
                'Jeton CSRF invalide.'
            );
        }

        $nouveauStatut = (string) $request->request->get('statut');

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
                ->from('no-reply@vite-et-gourmand.fr')
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
                ->from('no-reply@vite-et-gourmand.fr')
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

    // Ajoute un état daté sans écraser l'historique précédent.
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

    private function verifierNombreMinimum(Commande $commande): bool
    {
        return $commande->getNombrePersonnes()
            >= $commande->getMenu()->getNombreMinimumPersonnes();
    }

    // Recalcule toujours le prix côté serveur.
    private function calculerPrix(Commande $commande): void
    {
        $menu = $commande->getMenu();

        $prixMenu = (float) $menu->getPrixMinimum();
        $reduction = 0.0;

        if (
            $commande->getNombrePersonnes()
            >= $menu->getNombreMinimumPersonnes() + 5
        ) {
            $reduction = $prixMenu * 0.10;
        }

        $distance = max(
            0,
            $commande->getDistanceLivraison() ?? 0
        );

        $fraisLivraison = $distance > 0
            ? 5 + (0.59 * $distance)
            : 0;

        $total = $prixMenu - $reduction + $fraisLivraison;

        $commande->setDistanceLivraison($distance);
        $commande->setPrixMenu(number_format($prixMenu, 2, '.', ''));
        $commande->setReduction(number_format($reduction, 2, '.', ''));
        $commande->setFraisLivraison(
            number_format($fraisLivraison, 2, '.', '')
        );
        $commande->setPrixTotal(number_format($total, 2, '.', ''));
    }
}