<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mime\Address;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        MailerInterface $mailer
    ): Response {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $donnees = $form->getData();

            // L'adresse du visiteur est placée en Reply-To :
            // l'entreprise peut répondre directement sans usurper son adresse.
            $email = (new Email())
                ->from(new Address('hedi94220@gmail.com', 'Vite & Gourmand'))
                ->to('jose@vite-et-gourmand.fr')
                ->replyTo($donnees['email'])
                ->subject('Contact Vite & Gourmand - '.$donnees['titre'])
                ->text(
                    "Nouvelle demande de contact\n\n"
                    ."Adresse e-mail : ".$donnees['email']."\n"
                    ."Titre : ".$donnees['titre']."\n\n"
                    ."Message :\n".$donnees['description']
                );

            $mailer->send($email);

            $this->addFlash(
                'success',
                'Votre message a bien été envoyé.'
            );

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form,
        ]);
    }
}