<?php

namespace App\Controller;

use App\Entity\ImageMenu;
use App\Entity\Menu;
use App\Form\MenuType;
use App\Repository\ImageMenuRepository;
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
        return $this->render('admin_menu/index.html.twig', [
            'menus' => $menuRepository->findAll(),
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

            $this->addFlash(
                'success',
                'Le menu a été créé. Vous pouvez maintenant ajouter ses images.'
            );

            return $this->redirectToRoute('app_admin_menu_edit', [
                'id' => $menu->getId(),
            ]);
        }

        return $this->render('admin_menu/new.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * Modifie les informations du menu et affiche sa galerie d'images.
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

            return $this->redirectToRoute('app_admin_menu_edit', [
                'id' => $menu->getId(),
            ]);
        }

        return $this->render('admin_menu/edit.html.twig', [
            'menu' => $menu,
            'form' => $form,

            // Liste les images déjà présentes dans le projet.
            'imagesDisponibles' => $this->getImagesDisponibles(),
        ]);
    }

    /**
     * Ajoute à la galerie une image déjà disponible dans public/images/menus.
     */
    #[Route(
        '/admin/menus/{id}/images/ajouter',
        name: 'app_admin_menu_image_add',
        methods: ['POST']
    )]
    public function addImage(
        Menu $menu,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$this->isCsrfTokenValid(
            'add_image_menu_' . $menu->getId(),
            (string) $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $nomFichier = basename(
            (string) $request->request->get('nom_fichier')
        );

        if ($nomFichier === '') {
            $this->addFlash('danger', 'Veuillez sélectionner une image.');

            return $this->redirectToRoute('app_admin_menu_edit', [
                'id' => $menu->getId(),
            ]);
        }

        $chemin = $this->getParameter('kernel.project_dir')
            . '/public/images/menus/'
            . $nomFichier;

        // Empêche d'enregistrer un fichier absent du projet.
        if (!is_file($chemin)) {
            $this->addFlash('danger', 'Cette image est introuvable.');

            return $this->redirectToRoute('app_admin_menu_edit', [
                'id' => $menu->getId(),
            ]);
        }

        // Évite d'ajouter deux fois la même image au même menu.
        foreach ($menu->getImageMenus() as $imageExistante) {
            if ($imageExistante->getNomFichier() === $nomFichier) {
                $this->addFlash(
                    'warning',
                    'Cette image appartient déjà à la galerie.'
                );

                return $this->redirectToRoute('app_admin_menu_edit', [
                    'id' => $menu->getId(),
                ]);
            }
        }

        $image = new ImageMenu();
        $image->setNomFichier($nomFichier);
        $image->setMenu($menu);

        $entityManager->persist($image);
        $entityManager->flush();

        $this->addFlash('success', 'L’image a été ajoutée à la galerie.');

        return $this->redirectToRoute('app_admin_menu_edit', [
            'id' => $menu->getId(),
        ]);
    }

    /**
     * Supprime une image de la galerie du menu.
     * Le fichier physique reste disponible pour les autres menus.
     */
    #[Route(
        '/admin/menus/{id}/images/{imageId}/supprimer',
        name: 'app_admin_menu_image_delete',
        methods: ['POST']
    )]
    public function deleteImage(
        Menu $menu,
        int $imageId,
        Request $request,
        ImageMenuRepository $imageMenuRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $image = $imageMenuRepository->find($imageId);

        if (!$image || $image->getMenu()?->getId() !== $menu->getId()) {
            throw $this->createNotFoundException('Image introuvable.');
        }

        if (!$this->isCsrfTokenValid(
            'delete_image_menu_' . $image->getId(),
            (string) $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        /*
         * On supprime seulement l'association en base.
         * Le fichier reste dans public/images/menus pour être réutilisé.
         */
        $entityManager->remove($image);
        $entityManager->flush();

        $this->addFlash('success', 'L’image a été retirée de la galerie.');

        return $this->redirectToRoute('app_admin_menu_edit', [
            'id' => $menu->getId(),
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
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        /*
         * Une commande doit conserver le menu auquel elle était liée.
         * On interdit donc la suppression d'un menu déjà commandé.
         */
        if (!$menu->getCommandes()->isEmpty()) {
            $this->addFlash(
                'danger',
                'Ce menu ne peut pas être supprimé car il est lié à une ou plusieurs commandes.'
            );

            return $this->redirectToRoute('app_admin_menu_index');
        }

        /*
         * Les images possèdent une clé étrangère obligatoire vers le menu.
         * On supprime d'abord leurs enregistrements avant de supprimer le menu.
         */
        foreach ($menu->getImageMenus()->toArray() as $image) {
            $entityManager->remove($image);
        }

        $entityManager->remove($menu);
        $entityManager->flush();

        $this->addFlash('success', 'Le menu a été supprimé.');

        return $this->redirectToRoute('app_admin_menu_index');
    }

    /**
     * Récupère les noms des images présentes dans public/images/menus.
     */
    private function getImagesDisponibles(): array
    {
        $dossier = $this->getParameter('kernel.project_dir')
            . '/public/images/menus';

        if (!is_dir($dossier)) {
            return [];
        }

        $images = [];

        foreach (scandir($dossier) ?: [] as $nomFichier) {
            if (in_array($nomFichier, ['.', '..'], true)) {
                continue;
            }

            $extension = strtolower(
                pathinfo($nomFichier, PATHINFO_EXTENSION)
            );

            // On ne propose que les formats d'image classiques du projet.
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                $images[] = $nomFichier;
            }
        }

        sort($images);

        return $images;
    }
}