<?php

namespace App\Controller;

use App\Entity\Plat;
use App\Form\PlatType;
use App\Repository\PlatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/*
 * Les employés peuvent gérer les plats.
 * Les administrateurs héritent automatiquement du rôle employé.
 */
#[IsGranted('ROLE_EMPLOYE')]
final class AdminPlatController extends AbstractController
{
    /**
     * Affiche tous les plats disponibles pour leur gestion.
     */
    #[Route('/admin/plats', name: 'app_admin_plat_index', methods: ['GET'])]
    public function index(PlatRepository $platRepository): Response
    {
        $plats = $platRepository->findAll();

        return $this->render('admin_plat/index.html.twig', [
            'plats' => $plats,
        ]);
    }

    /**
     * Crée un nouveau plat avec ses éventuels allergènes.
     */
    #[Route('/admin/plats/nouveau', name: 'app_admin_plat_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $plat = new Plat();

        $form = $this->createForm(PlatType::class, $plat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($plat);
            $entityManager->flush();

            $this->addFlash('success', 'Le plat a été créé.');

            return $this->redirectToRoute('app_admin_plat_index');
        }

        return $this->render('admin_plat/new.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * Modifie un plat existant et ses allergènes.
     */
    #[Route('/admin/plats/{id}/modifier', name: 'app_admin_plat_edit', methods: ['GET', 'POST'])]
    public function edit(
        Plat $plat,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(PlatType::class, $plat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le plat a été modifié.');

            return $this->redirectToRoute('app_admin_plat_index');
        }

        return $this->render('admin_plat/edit.html.twig', [
            'plat' => $plat,
            'form' => $form,
        ]);
    }

    /**
     * Supprime un plat après vérification du jeton CSRF.
     */
    #[Route('/admin/plats/{id}/supprimer', name: 'app_admin_plat_delete', methods: ['POST'])]
    public function delete(
        Plat $plat,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$this->isCsrfTokenValid(
            'delete_plat_' . $plat->getId(),
            (string) $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException(
                'Jeton CSRF invalide.'
            );
        }

        $entityManager->remove($plat);
        $entityManager->flush();

        $this->addFlash('success', 'Le plat a été supprimé.');

        return $this->redirectToRoute('app_admin_plat_index');
    }
}