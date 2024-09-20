<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240920070312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE export_logs ADD user_id INT DEFAULT NULL, ADD inventory_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE export_logs ADD CONSTRAINT FK_D63EF0B7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE export_logs ADD CONSTRAINT FK_D63EF0B79EEA759 FOREIGN KEY (inventory_id) REFERENCES inventory (id)');
        $this->addSql('CREATE INDEX IDX_D63EF0B7A76ED395 ON export_logs (user_id)');
        $this->addSql('CREATE INDEX IDX_D63EF0B79EEA759 ON export_logs (inventory_id)');
        $this->addSql('ALTER TABLE import_logs ADD user_id INT DEFAULT NULL, ADD inventory_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE import_logs ADD CONSTRAINT FK_1DA328DCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE import_logs ADD CONSTRAINT FK_1DA328DC9EEA759 FOREIGN KEY (inventory_id) REFERENCES inventory (id)');
        $this->addSql('CREATE INDEX IDX_1DA328DCA76ED395 ON import_logs (user_id)');
        $this->addSql('CREATE INDEX IDX_1DA328DC9EEA759 ON import_logs (inventory_id)');
        $this->addSql('ALTER TABLE room ADD etablishment_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B16BE0BCF FOREIGN KEY (etablishment_id) REFERENCES etablishment (id)');
        $this->addSql('CREATE INDEX IDX_729F519B16BE0BCF ON room (etablishment_id)');
        $this->addSql('ALTER TABLE user ADD etablishment_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64916BE0BCF FOREIGN KEY (etablishment_id) REFERENCES etablishment (id)');
        $this->addSql('CREATE INDEX IDX_8D93D64916BE0BCF ON user (etablishment_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64916BE0BCF');
        $this->addSql('DROP INDEX IDX_8D93D64916BE0BCF ON user');
        $this->addSql('ALTER TABLE user DROP etablishment_id');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B16BE0BCF');
        $this->addSql('DROP INDEX IDX_729F519B16BE0BCF ON room');
        $this->addSql('ALTER TABLE room DROP etablishment_id');
        $this->addSql('ALTER TABLE export_logs DROP FOREIGN KEY FK_D63EF0B7A76ED395');
        $this->addSql('ALTER TABLE export_logs DROP FOREIGN KEY FK_D63EF0B79EEA759');
        $this->addSql('DROP INDEX IDX_D63EF0B7A76ED395 ON export_logs');
        $this->addSql('DROP INDEX IDX_D63EF0B79EEA759 ON export_logs');
        $this->addSql('ALTER TABLE export_logs DROP user_id, DROP inventory_id');
        $this->addSql('ALTER TABLE import_logs DROP FOREIGN KEY FK_1DA328DCA76ED395');
        $this->addSql('ALTER TABLE import_logs DROP FOREIGN KEY FK_1DA328DC9EEA759');
        $this->addSql('DROP INDEX IDX_1DA328DCA76ED395 ON import_logs');
        $this->addSql('DROP INDEX IDX_1DA328DC9EEA759 ON import_logs');
        $this->addSql('ALTER TABLE import_logs DROP user_id, DROP inventory_id');
    }
}
