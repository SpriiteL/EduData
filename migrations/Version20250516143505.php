<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250516143505 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE printer_stat ADD job_charge_count_fcl INT NOT NULL, ADD job_charge_count_fcs INT NOT NULL, ADD impression_total_couleur INT NOT NULL, ADD job_charge_count_mtl INT NOT NULL, ADD job_charge_count_mts INT NOT NULL, ADD impression_total_mono INT NOT NULL, ADD job_charge_count_mcl INT NOT NULL, ADD job_charge_count_mcs INT NOT NULL, ADD copie_total_couleur INT NOT NULL, ADD job_charge_count_mbl INT NOT NULL, ADD job_charge_count_mbs INT NOT NULL, ADD copie_total_mono INT NOT NULL, DROP imp_couleur_a4, DROP imp_couleur_a3, DROP imp_couleur_total, DROP imp_noir_a4, DROP imp_noir_a3, DROP imp_noir_total, DROP copie_couleur_a4, DROP copie_couleur_a3, DROP copie_couleur_total, DROP copie_noir_a4, DROP copie_noir_a3, DROP copie_noir_total
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE printer_stat ADD imp_couleur_a4 INT NOT NULL, ADD imp_couleur_a3 INT NOT NULL, ADD imp_couleur_total INT NOT NULL, ADD imp_noir_a4 INT NOT NULL, ADD imp_noir_a3 INT NOT NULL, ADD imp_noir_total INT NOT NULL, ADD copie_couleur_a4 INT NOT NULL, ADD copie_couleur_a3 INT NOT NULL, ADD copie_couleur_total INT NOT NULL, ADD copie_noir_a4 INT NOT NULL, ADD copie_noir_a3 INT NOT NULL, ADD copie_noir_total INT NOT NULL, DROP job_charge_count_fcl, DROP job_charge_count_fcs, DROP impression_total_couleur, DROP job_charge_count_mtl, DROP job_charge_count_mts, DROP impression_total_mono, DROP job_charge_count_mcl, DROP job_charge_count_mcs, DROP copie_total_couleur, DROP job_charge_count_mbl, DROP job_charge_count_mbs, DROP copie_total_mono
        SQL);
    }
}
