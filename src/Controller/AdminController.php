<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\EmployeType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

#[IsGranted('ROLE_ADMINISTRATEUR')]
final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        $employes = array_filter(
    $utilisateurRepository->findAll(),
    fn (Utilisateur $utilisateur) =>
        in_array('ROLE_EMPLOYE', $utilisateur->getRoles(), true)
        && !in_array('ROLE_ADMINISTRATEUR', $utilisateur->getRoles(), true)
);

        return $this->render('admin/index.html.twig', [
            'employes' => $employes,
        ]);
    }

    #[Route('/admin/employe/nouveau', name: 'app_admin_employe_nouveau')]
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
            $employe->setRoles(['ROLE_EMPLOYE']);

            $employe->setPassword(
                $passwordHasher->hashPassword(
                    $employe,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($employe);
            $entityManager->flush();

            $email = (new TemplatedEmail())
    ->from('contact@vite-et-gourmand.fr')
    ->to($employe->getEmail())
    ->subject('Création de votre compte Vite & Gourmand')
    ->htmlTemplate('emails/creation_employe.html.twig');

$mailer->send($email);

            $this->addFlash('success', 'Le compte employé a été créé.');

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/nouvel_employe.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/admin/employe/{id}/statut', name: 'app_admin_employe_statut', methods: ['POST'])]
public function changerStatutEmploye(
    Utilisateur $employe,
    Request $request,
    EntityManagerInterface $entityManager
): Response {
    // Vérifie que le compte concerné est bien un employé
    if (!in_array('ROLE_EMPLOYE', $employe->getRoles(), true)) {
        throw $this->createAccessDeniedException();
    }

    // Vérifie le token CSRF envoyé par le formulaire
    if (!$this->isCsrfTokenValid(
        'statut_employe_'.$employe->getId(),
        $request->request->get('_token')
    )) {
        throw $this->createAccessDeniedException('Jeton CSRF invalide.');
    }

    // Inverse le statut actif/désactivé
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