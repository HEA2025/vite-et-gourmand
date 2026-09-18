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

#[IsGranted('ROLE_ADMINISTRATEUR')]
final class AdminMenuController extends AbstractController
{
    #[Route('/admin/menus', name: 'app_admin_menu_index', methods: ['GET'])]
    public function index(MenuRepository $menuRepository): Response
    {
        $menus = $menuRepository->findAll();

        return $this->render('admin_menu/index.html.twig', [
            'menus' => $menus,
        ]);
    }

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

    #[Route('/admin/menus/{id}/supprimer', name: 'app_admin_menu_delete', methods: ['POST'])]
    public function delete(
        Menu $menu,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid(
            'delete_menu_' . $menu->getId(),
            $request->request->get('_token')
        )) {
            $entityManager->remove($menu);
            $entityManager->flush();

            $this->addFlash('success', 'Le menu a été supprimé.');
        }

        return $this->redirectToRoute('app_admin_menu_index');
    }
}