<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Utilisateur>
 */
class UtilisateurRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    /**
     * Met à jour automatiquement le hash du mot de passe lorsque Symfony le demande.
     */
    public function upgradePassword(
        PasswordAuthenticatedUserInterface $user,
        string $newHashedPassword
    ): void {
        if (!$user instanceof Utilisateur) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', $user::class)
            );
        }

        $user->setPassword($newHashedPassword);

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Retourne uniquement les comptes ayant réellement le rôle employé.
     * Les administrateurs ne sont pas inclus dans cette liste.
     *
     * @return Utilisateur[]
     */
    public function findEmployes(): array
    {
        $utilisateurs = $this->findBy([], ['email' => 'ASC']);

        return array_values(array_filter(
            $utilisateurs,
            static function (Utilisateur $utilisateur): bool {
                $roles = $utilisateur->getRoles();

                return in_array('ROLE_EMPLOYE', $roles, true)
                    && !in_array('ROLE_ADMINISTRATEUR', $roles, true);
            }
        ));
    }
}