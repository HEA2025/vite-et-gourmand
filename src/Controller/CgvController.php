<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CgvController extends AbstractController
{
    /**
     * Affiche les conditions générales de vente.
     */
    #[Route('/conditions-generales-de-vente', name: 'app_cgv', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('cgv/index.html.twig');
    }
}