<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commande>
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    /**
     * Recherche les commandes par client.
     *
     * @return Commande[]
     */
    public function findByClient(?string $client): array
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.utilisateur', 'u')
            ->addSelect('u')
            ->orderBy('c.dateCreation', 'DESC');

        if ($client !== null && trim($client) !== '') {
            $recherche = '%' . mb_strtolower(trim($client)) . '%';

            $qb
                ->andWhere(
                    'LOWER(u.nom) LIKE :client
                    OR LOWER(u.prenom) LIKE :client
                    OR LOWER(u.email) LIKE :client'
                )
                ->setParameter('client', $recherche);
        }

        return $qb->getQuery()->getResult();
    }
}