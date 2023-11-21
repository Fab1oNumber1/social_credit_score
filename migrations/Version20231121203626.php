<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231121203626 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, transaction_id INT DEFAULT NULL, transaction_comment_id INT DEFAULT NULL, created DATETIME DEFAULT NULL, updated DATETIME DEFAULT NULL, active TINYINT(1) NOT NULL, message VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_BF5476CA2FC0CB0F (transaction_id), INDEX IDX_BF5476CA17FF4AA2 (transaction_comment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA2FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transaction (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA17FF4AA2 FOREIGN KEY (transaction_comment_id) REFERENCES transaction_comment (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA2FC0CB0F');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA17FF4AA2');
        $this->addSql('DROP TABLE notification');
    }
}
