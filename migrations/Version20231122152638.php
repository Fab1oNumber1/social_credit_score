<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231122152638 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction ADD media_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1EA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id)');
        $this->addSql('CREATE INDEX IDX_723705D1EA9FDD75 ON transaction (media_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1EA9FDD75');
        $this->addSql('DROP INDEX IDX_723705D1EA9FDD75 ON transaction');
        $this->addSql('ALTER TABLE transaction DROP media_id');
    }
}
