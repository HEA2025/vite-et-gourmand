<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Form\MenuType;
use App\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/*
 * Les employés peuvent gérer les menus.
 * Grâce à la hiérarchie des rôles, les administrateurs y ont aussi accès.
 */
#[IsGranted('ROLE_EMPLOYE')]
final class AdminMenuController extends AbstractController
{
    /**
     * Affiche tous les menus disponibles pour leur gestion.
     */
    #[Route('/admin/menus', name: 'app_admin_menu_index', methods: ['GET'])]
    public function index(MenuRepository $menuRepository): Response
    {
        $menus = $menuRepository->findAll();

        return $this->render('admin_menu/index.html.twig', [
            'menus' => $menus,
        ]);
    }

    /**
     * Crée un nouveau menu à partir du formulaire Symfony.
     */
    #[Route('/admin/menus/nouveau', name: 'app_admin_menu_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $menu = new Menu();

        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($menu);
            $entityManager->flush();

            $this->addFlash('success', 'Le menu a été créé.');

            return $this->redirectToRoute('app_admin_menu_index');
        }

        return $this->render('admin_menu/new.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * Modifie les informations d'un menu existant.
     * Le stock peut notamment être ajusté depuis ce formulaire.
     */
    #[Route('/admin/menus/{id}/modifier', name: 'app_admin_menu_edit', methods: ['GET', 'POST'])]
    public function edit(
        Menu $menu,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le menu a été modifié.');

            return $this->redirectToRoute('app_admin_menu_index');
        }

        return $this->render('admin_menu/edit.html.twig', [
            'menu' => $menu,
            'form' => $form,
        ]);
    }

    /**
     * Supprime un menu après vérification du jeton CSRF.
     */
    #[Route('/admin/menus/{id}/supprimer', name: 'app_admin_menu_delete', methods: ['POST'])]
    public function delete(
        Menu $menu,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$this->isCsrfTokenValid(
            'delete_menu_' . $menu->getId(),
            (string) $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException(
                'Jeton CSRF invalide.'
            );
        }

        $entityManager->remove($menu);
        $entityManager->flush();

        $this->addFlash('success', 'Le menu a été supprimé.');

        return $this->redirectToRoute('app_admin_menu_index');
    }
}