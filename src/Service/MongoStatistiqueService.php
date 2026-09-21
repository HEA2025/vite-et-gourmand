<?php

namespace App\Service;

use App\Entity\Commande;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\BSON\UTCDateTime;

class MongoStatistiqueService
{
    private Collection $collection;

    public function __construct()
    {
        // Utilise la configuration d'environnement si elle existe.
        $mongoUrl = $_ENV['MONGODB_URL'] ?? 'mongodb://127.0.0.1:27017';

        $client = new Client($mongoUrl);

        // Collection NoSQL réservée aux statistiques des commandes.
        $this->collection = $client
            ->selectDatabase('vite_et_gourmand')
            ->selectCollection('statistiques_commandes');
    }

    /**
     * Synchronise une commande MySQL dans MongoDB.
     */
    public function synchroniserCommande(Commande $commande): void
    {
        $menu = $commande->getMenu();

        if ($commande->getId() === null || $menu === null || $menu->getId() === null) {
            return;
        }

        $statutActuel = $this->getStatutActuel($commande);

        // Une commande annulée ne participe pas aux statistiques commerciales.
        if ($statutActuel === 'annulée') {
            $this->collection->deleteOne([
                'commande_id' => $commande->getId(),
            ]);

            return;
        }

        $dateCommande = $commande->getDateCreation();

        if ($dateCommande === null) {
            return;
        }

        // Un document MongoDB contient les données utiles aux statistiques.
        $document = [
            'commande_id' => $commande->getId(),
            'menu_id' => $menu->getId(),
            'menu_titre' => $menu->getTitre(),
            'date_commande' => new UTCDateTime(
                $dateCommande->getTimestamp() * 1000
            ),
            'prix_total' => (float) $commande->getPrixTotal(),
            'statut' => $statutActuel,
        ];

        // Upsert évite de créer plusieurs documents pour la même commande.
        $this->collection->updateOne(
            ['commande_id' => $commande->getId()],
            ['$set' => $document],
            ['upsert' => true]
        );
    }

    /**
     * Supprime les anciennes statistiques avant une synchronisation complète.
     */
    public function viderCollection(): void
    {
        $this->collection->deleteMany([]);
    }

    /**
     * Retourne le nombre de commandes pour chaque menu depuis MongoDB.
     */
    public function getNombreCommandesParMenu(): array
    {
        $pipeline = [
            [
                '$group' => [
                    '_id' => [
                        'menu_id' => '$menu_id',
                        'menu_titre' => '$menu_titre',
                    ],
                    'nombre_commandes' => ['$sum' => 1],
                ],
            ],
            [
                '$sort' => [
                    'nombre_commandes' => -1,
                ],
            ],
        ];

        $resultats = [];

        foreach ($this->collection->aggregate($pipeline) as $document) {
            $resultats[] = [
                'menu_id' => (int) $document->_id->menu_id,
                'menu_titre' => (string) $document->_id->menu_titre,
                'nombre_commandes' => (int) $document->nombre_commandes,
            ];
        }

        return $resultats;
    }

    /**
     * Calcule le chiffre d'affaires avec filtres facultatifs.
     */
    public function getChiffreAffaires(
        ?int $menuId = null,
        ?\DateTimeImmutable $dateDebut = null,
        ?\DateTimeImmutable $dateFin = null
    ): float {
        $filtre = [];

        if ($menuId !== null) {
            $filtre['menu_id'] = $menuId;
        }

        if ($dateDebut !== null || $dateFin !== null) {
            $filtre['date_commande'] = [];

            if ($dateDebut !== null) {
                $filtre['date_commande']['$gte'] = new UTCDateTime(
                    $dateDebut->setTime(0, 0, 0)->getTimestamp() * 1000
                );
            }

            if ($dateFin !== null) {
                $filtre['date_commande']['$lte'] = new UTCDateTime(
                    $dateFin->setTime(23, 59, 59)->getTimestamp() * 1000
                );
            }
        }

        $pipeline = [];

        if ($filtre !== []) {
            $pipeline[] = ['$match' => $filtre];
        }

        $pipeline[] = [
            '$group' => [
                '_id' => null,
                'chiffre_affaires' => ['$sum' => '$prix_total'],
            ],
        ];

        $resultat = $this->collection->aggregate($pipeline)->toArray();

        if ($resultat === []) {
            return 0.0;
        }

        return (float) $resultat[0]->chiffre_affaires;
    }

    /**
     * Retourne les menus présents dans les données statistiques MongoDB.
     */
    public function getMenus(): array
    {
        $pipeline = [
            [
                '$group' => [
                    '_id' => '$menu_id',
                    'titre' => ['$first' => '$menu_titre'],
                ],
            ],
            [
                '$sort' => [
                    'titre' => 1,
                ],
            ],
        ];

        $menus = [];

        foreach ($this->collection->aggregate($pipeline) as $document) {
            $menus[] = [
                'id' => (int) $document->_id,
                'titre' => (string) $document->titre,
            ];
        }

        return $menus;
    }

    /**
     * Recherche le dernier statut enregistré dans l'historique.
     */
    private function getStatutActuel(Commande $commande): ?string
    {
        $dernierStatut = null;
        $derniereDate = null;

        foreach ($commande->getHistoriqueStatutCommandes() as $historique) {
            $date = $historique->getDateModification();

            if (
                $date !== null
                && ($derniereDate === null || $date > $derniereDate)
            ) {
                $derniereDate = $date;
                $dernierStatut = $historique->getStatut();
            }
        }

        return $dernierStatut;
    }
}