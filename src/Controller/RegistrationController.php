<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
        $user = new Utilisateur();

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            // Un compte créé depuis l'inscription publique est toujours un client.
            $user->setRoles(['ROLE_USER']);

            // Le mot de passe est hashé avant son enregistrement en base.
            $user->setPassword(
                $userPasswordHasher->hashPassword($user, $plainPassword)
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // Envoie automatiquement l'email de bienvenue demandé par l'ECF.
            $email = (new TemplatedEmail())
                ->from('hedi94220@gmail.com')
                ->to($user->getEmail())
                ->subject('Bienvenue chez Vite & Gourmand')
                ->htmlTemplate('emails/bienvenue.html.twig')
                ->context([
                    'utilisateur' => $user,
                ]);

            $mailer->send($email);

            // Informe l'utilisateur que son inscription est terminée.
            $this->addFlash(
                'success',
                'Votre compte a été créé. Un email de bienvenue vous a été envoyé.'
            );

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}