<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\EmployeType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Gestion des comptes employés réservée à l'administrateur.
 */
#[IsGranted('ROLE_ADMINISTRATEUR')]
final class AdminController extends AbstractController
{
    /**
     * Affiche la liste des employés et leur statut.
     */
    #[Route('/admin', name: 'app_admin', methods: ['GET'])]
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'employes' => $utilisateurRepository->findEmployes(),
        ]);
    }

    /**
     * Crée un compte employé avec un mot de passe hashé.
     */
    #[Route(
        '/admin/employe/nouveau',
        name: 'app_admin_employe_nouveau',
        methods: ['GET', 'POST']
    )]
    public function nouvelEmploye(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
        $employe = new Utilisateur();

        $form = $this->createForm(EmployeType::class, $employe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Le rôle est imposé côté serveur et ne peut pas être choisi dans le formulaire.
            $employe->setRoles(['ROLE_EMPLOYE']);
            $employe->setActif(true);

            // Seul le hash du mot de passe est enregistré dans MySQL.
            $employe->setPassword(
                $passwordHasher->hashPassword(
                    $employe,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($employe);
            $entityManager->flush();

            // Informe l'employé de la création de son compte sans envoyer son mot de passe.
            $email = (new TemplatedEmail())
                ->from('contact@vite-et-gourmand.fr')
                ->to($employe->getEmail())
                ->subject('Création de votre compte Vite & Gourmand')
                ->htmlTemplate('emails/creation_employe.html.twig')
                ->context([
                    'employe' => $employe,
                ]);

            $mailer->send($email);

            $this->addFlash('success', 'Le compte employé a été créé.');

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/nouvel_employe.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * Active ou désactive un employé sans supprimer son compte.
     */
    #[Route(
        '/admin/employe/{id}/statut',
        name: 'app_admin_employe_statut',
        requirements: ['id' => '\d+'],
        methods: ['POST']
    )]
    public function changerStatutEmploye(
        Utilisateur $employe,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $roles = $employe->getRoles();

        // Cette fonctionnalité concerne uniquement les comptes employés.
        if (
            !in_array('ROLE_EMPLOYE', $roles, true)
            || in_array('ROLE_ADMINISTRATEUR', $roles, true)
        ) {
            throw $this->createAccessDeniedException(
                'Ce compte ne peut pas être géré comme un employé.'
            );
        }

        // Protège le changement de statut contre les requêtes POST frauduleuses.
        if (!$this->isCsrfTokenValid(
            'statut_employe_'.$employe->getId(),
            (string) $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        // Le compte reste en base afin de conserver les données liées à l'employé.
        $employe->setActif(!$employe->isActif());

        $entityManager->flush();

        $this->addFlash(
            'success',
            $employe->isActif()
                ? 'Le compte employé a été réactivé.'
                : 'Le compte employé a été désactivé.'
        );

        return $this->redirectToRoute('app_admin');
    }
}