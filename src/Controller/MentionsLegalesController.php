<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MentionsLegalesController extends AbstractController
{
    /**
     * Affiche la page des mentions légales accessible depuis le footer.
     */
    #[Route('/mentions-legales', name: 'app_mentions_legales', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('mentions_legales/index.html.twig');
    }
}