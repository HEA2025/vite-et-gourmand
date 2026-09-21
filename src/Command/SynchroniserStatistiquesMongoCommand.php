<?php

namespace App\Command;

use App\Repository\CommandeRepository;
use App\Service\MongoStatistiqueService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:mongo:sync-statistiques',
    description: 'Synchronise les commandes MySQL vers les statistiques MongoDB.'
)]
class SynchroniserStatistiquesMongoCommand extends Command
{
    public function __construct(
        private readonly CommandeRepository $commandeRepository,
        private readonly MongoStatistiqueService $mongoStatistiqueService
    ) {
        parent::__construct();
    }

    /**
     * Reconstruit les statistiques MongoDB à partir des commandes MySQL.
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        // Vide les anciennes statistiques pour éviter les données obsolètes.
        $this->mongoStatistiqueService->viderCollection();

        $commandes = $this->commandeRepository->findAll();
        $nombreSynchronise = 0;

        foreach ($commandes as $commande) {
            $this->mongoStatistiqueService->synchroniserCommande($commande);
            ++$nombreSynchronise;
        }

        $output->writeln(sprintf(
            '<info>Synchronisation terminée : %d commande(s) analysée(s).</info>',
            $nombreSynchronise
        ));

        return Command::SUCCESS;
    }
}