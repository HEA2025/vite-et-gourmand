<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class ProfilController extends AbstractController
{
    /**
     * Permet à l'utilisateur connecté de modifier ses informations personnelles.
     */
    #[Route('/mon-profil', name: 'app_profil_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $utilisateur = $this->getUser();

        // La route est protégée, mais on vérifie aussi le type de l'utilisateur.
        if (!$utilisateur instanceof Utilisateur) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(ProfilType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /*
             * L'utilisateur existe déjà en base :
             * persist() n'est donc pas nécessaire.
             */
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Vos informations personnelles ont été mises à jour.'
            );

            return $this->redirectToRoute('app_profil_edit');
        }

        return $this->render('profil/edit.html.twig', [
            'form' => $form,
        ]);
    }
}