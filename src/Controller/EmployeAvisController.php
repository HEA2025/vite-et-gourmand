<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/employe/avis')]
class EmployeAvisController extends AbstractController
{
    #[Route('', name: 'app_employe_avis_index', methods: ['GET'])]
    public function index(AvisRepository $avisRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        // Les avis les plus récents sont présentés en premier.
        $avis = $avisRepository->findBy(
            [],
            ['dateCreation' => 'DESC']
        );

        return $this->render('employe/avis/index.html.twig', [
            'avisListe' => $avis,
        ]);
    }

    #[Route(
        '/{id}/statut',
        name: 'app_employe_avis_statut',
        methods: ['POST']
    )]
    public function changerStatut(
        Avis $avis,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        if (!$this->isCsrfTokenValid(
            'statut_avis_' . $avis->getId(),
            (string) $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException(
                'Jeton CSRF invalide.'
            );
        }

        $statut = (string) $request->request->get('statut');

        if (!in_array($statut, ['validé', 'refusé'], true)) {
            $this->addFlash('danger', 'Statut d’avis invalide.');

            return $this->redirectToRoute('app_employe_avis_index');
        }

        $avis->setStatut($statut);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Le statut de l’avis a été mis à jour.'
        );

        return $this->redirectToRoute('app_employe_avis_index');
    }
}