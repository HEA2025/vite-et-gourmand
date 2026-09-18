<?php

namespace App\Controller;

use App\Repository\MenuRepository;
use App\Repository\RegimeRepository;
use App\Repository\ThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MenuController extends AbstractController
{
    #[Route('/menus', name: 'app_menu_index', methods: ['GET'])]
    public function index(
        MenuRepository $menuRepository,
        ThemeRepository $themeRepository,
        RegimeRepository $regimeRepository
    ): Response {
        return $this->render('menu/index.html.twig', [
            'menus' => $menuRepository->findAll(),
            'themes' => $themeRepository->findAll(),
            'regimes' => $regimeRepository->findAll(),
        ]);
    }

    #[Route('/menus/filtrer', name: 'app_menu_filter', methods: ['GET'])]
    public function filter(
        Request $request,
        MenuRepository $menuRepository
    ): Response {
        $theme = $request->query->get('theme');
        $regime = $request->query->get('regime');
        $prixMin = $request->query->get('prixMin');
        $prixMax = $request->query->get('prixMax');
        $personnes = $request->query->get('personnes');

        $menus = $menuRepository->findAll();
        $resultat = [];

        foreach ($menus as $menu) {
            if ($theme && $menu->getTheme()->getId() != $theme) {
                continue;
            }

            if ($regime && $menu->getRegime()->getId() != $regime) {
                continue;
            }

            if ($prixMin && $menu->getPrixMinimum() < $prixMin) {
                continue;
            }

            if ($prixMax && $menu->getPrixMinimum() > $prixMax) {
                continue;
            }

            if ($personnes && $menu->getNombreMinimumPersonnes() > $personnes) {
                continue;
            }

            $resultat[] = [
                'id' => $menu->getId(),
                'titre' => $menu->getTitre(),
                'description' => $menu->getDescription(),
                'theme' => $menu->getTheme()->getNom(),
                'regime' => $menu->getRegime()->getNom(),
                'prix' => $menu->getPrixMinimum(),
                'minimumPersonnes' => $menu->getNombreMinimumPersonnes(),
                'stock' => $menu->getStock(),
            ];
        }

        return $this->json($resultat);
    }

    #[Route('/menus/{id}', name: 'app_menu_show', methods: ['GET'])]
    public function show(int $id, MenuRepository $menuRepository): Response
    {
        $menu = $menuRepository->find($id);

        if (!$menu) {
            throw $this->createNotFoundException('Menu introuvable.');
        }

        return $this->render('menu/show.html.twig', [
            'menu' => $menu,
        ]);
    }
}