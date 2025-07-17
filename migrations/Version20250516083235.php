<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250516083235 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE printer_stat (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, imp_couleur_a4 INT NOT NULL, imp_couleur_a3 INT NOT NULL, imp_couleur_total INT NOT NULL, imp_noir_a4 INT NOT NULL, imp_noir_a3 INT NOT NULL, imp_noir_total INT NOT NULL, copie_couleur_a4 INT NOT NULL, copie_couleur_a3 INT NOT NULL, copie_couleur_total INT NOT NULL, copie_noir_a4 INT NOT NULL, copie_noir_a3 INT NOT NULL, copie_noir_total INT NOT NULL, total_couleur INT NOT NULL, total_noir INT NOT NULL, scan_a4 INT NOT NULL, scan_a3 INT NOT NULL, total_scan INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE printer_stat
        SQL);
    }
}
