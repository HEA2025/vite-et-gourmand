<?php

namespace App\Controller;

use App\Service\MongoStatistiqueService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMINISTRATEUR')]
final class AdminStatistiqueController extends AbstractController
{
    /**
     * Affiche les statistiques MongoDB réservées à l'administrateur.
     */
    #[Route(
        '/admin/statistiques',
        name: 'app_admin_statistiques',
        methods: ['GET']
    )]
    public function index(
        Request $request,
        MongoStatistiqueService $mongoStatistiqueService
    ): Response {
        /*
         * "Tous les menus" envoie une chaîne vide.
         * On la transforme en null pour signifier qu'aucun menu précis
         * ne doit être utilisé comme filtre.
         */
        $menuParametre = $request->query->get('menu');

        $menuId = is_string($menuParametre)
            && ctype_digit($menuParametre)
            && (int) $menuParametre > 0
                ? (int) $menuParametre
                : null;

        $dateDebut = $this->convertirDate(
            $request->query->get('date_debut')
        );

        $dateFin = $this->convertirDate(
            $request->query->get('date_fin')
        );

        // Empêche une période incohérente.
        if (
            $dateDebut !== null
            && $dateFin !== null
            && $dateDebut > $dateFin
        ) {
            $this->addFlash(
                'danger',
                'La date de début doit être antérieure à la date de fin.'
            );

            $dateDebut = null;
            $dateFin = null;
        }

        // Ces données MongoDB servent à comparer le nombre de commandes par menu.
        $statistiques = $mongoStatistiqueService
            ->getNombreCommandesParMenu();

        $maximumCommandes = 0;

        foreach ($statistiques as $statistique) {
            $maximumCommandes = max(
                $maximumCommandes,
                $statistique['nombre_commandes']
            );
        }

        // Le chiffre d'affaires peut être filtré par menu et par période.
        $chiffreAffaires = $mongoStatistiqueService
            ->getChiffreAffaires(
                $menuId,
                $dateDebut,
                $dateFin
            );

        return $this->render('admin/statistiques.html.twig', [
            'statistiques' => $statistiques,
            'maximumCommandes' => $maximumCommandes,
            'chiffreAffaires' => $chiffreAffaires,
            'menus' => $mongoStatistiqueService->getMenus(),
            'menuSelectionne' => $menuId,
            'dateDebut' => $dateDebut?->format('Y-m-d'),
            'dateFin' => $dateFin?->format('Y-m-d'),
        ]);
    }

    /**
     * Convertit une date du formulaire sans provoquer d'erreur si elle est vide
     * ou invalide.
     */
    private function convertirDate(?string $date): ?\DateTimeImmutable
    {
        if ($date === null || $date === '') {
            return null;
        }

        $dateConvertie = \DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $date
        );

        return $dateConvertie ?: null;
    }
}