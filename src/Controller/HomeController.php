<?php

namespace App\Controller;

use App\Repository\AvisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(AvisRepository $avisRepository): Response
    {
        // Seuls les avis validés par un employé sont visibles publiquement.
        $avisValides = $avisRepository->findBy(
            ['statut' => 'validé'],
            ['dateCreation' => 'DESC']
        );

        return $this->render('home/index.html.twig', [
            'avisValides' => $avisValides,
        ]);
    }
}