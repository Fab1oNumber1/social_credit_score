<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231121130658 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE transaction_comment (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, transaction_id INT NOT NULL, created DATETIME DEFAULT NULL, updated DATETIME DEFAULT NULL, active TINYINT(1) NOT NULL, text LONGTEXT NOT NULL, INDEX IDX_E2DE6E8EF675F31B (author_id), INDEX IDX_E2DE6E8E2FC0CB0F (transaction_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE transaction_comment ADD CONSTRAINT FK_E2DE6E8EF675F31B FOREIGN KEY (author_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE transaction_comment ADD CONSTRAINT FK_E2DE6E8E2FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transaction (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction_comment DROP FOREIGN KEY FK_E2DE6E8EF675F31B');
        $this->addSql('ALTER TABLE transaction_comment DROP FOREIGN KEY FK_E2DE6E8E2FC0CB0F');
        $this->addSql('DROP TABLE transaction_comment');
    }
}
