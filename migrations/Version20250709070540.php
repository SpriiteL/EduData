<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250709070540 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE printer_usage (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, total_black INT NOT NULL, total_color INT NOT NULL, total_scans INT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE printer_stat
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE printer_stat (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, total_couleur INT NOT NULL, total_noir INT NOT NULL, scan_a4 INT NOT NULL, scan_a3 INT NOT NULL, total_scan INT NOT NULL, job_charge_count_fcl INT NOT NULL, job_charge_count_fcs INT NOT NULL, impression_total_couleur INT NOT NULL, job_charge_count_mtl INT NOT NULL, job_charge_count_mts INT NOT NULL, impression_total_mono INT NOT NULL, job_charge_count_mcl INT NOT NULL, job_charge_count_mcs INT NOT NULL, copie_total_couleur INT NOT NULL, job_charge_count_mbl INT NOT NULL, job_charge_count_mbs INT NOT NULL, copie_total_mono INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE printer_usage
        SQL);
    }
}
