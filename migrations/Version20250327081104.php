<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250327081104 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_print_stats (id INT AUTO_INCREMENT NOT NULL, owner_name VARCHAR(255) NOT NULL, impression_couleur_a4 INT NOT NULL, impression_couleur_sup_a4 INT NOT NULL, impression_total_couleur INT NOT NULL, impression_mono_a4 INT NOT NULL, impression_mono_sup_a4 INT NOT NULL, impression_total_mono INT NOT NULL, copie_couleur_a4 INT NOT NULL, copie_couleur_sup_a4 INT NOT NULL, copie_total_couleur INT NOT NULL, copie_mono_a4 INT NOT NULL, copie_mono_sup_a4 INT NOT NULL, copie_total_mono INT NOT NULL, total_couleur INT NOT NULL, total_noir INT NOT NULL, scan_a4 INT NOT NULL, scan_a3 INT NOT NULL, total_scans INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_print_stats');
    }
}
