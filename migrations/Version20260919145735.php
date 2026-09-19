<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260919145735 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande ADD materiel_prete TINYINT DEFAULT 0 NOT NULL, ADD moyen_contact_employe VARCHAR(20) DEFAULT NULL, ADD motif_intervention_employe LONGTEXT DEFAULT NULL, ADD date_contact_employe DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP materiel_prete, DROP moyen_contact_employe, DROP motif_intervention_employe, DROP date_contact_employe');
    }
}
