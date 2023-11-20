<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231120225454 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE transaction_user (transaction_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_6FD0E8E82FC0CB0F (transaction_id), INDEX IDX_6FD0E8E8A76ED395 (user_id), PRIMARY KEY(transaction_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE transaction_user ADD CONSTRAINT FK_6FD0E8E82FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transaction (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE transaction_user ADD CONSTRAINT FK_6FD0E8E8A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction_user DROP FOREIGN KEY FK_6FD0E8E82FC0CB0F');
        $this->addSql('ALTER TABLE transaction_user DROP FOREIGN KEY FK_6FD0E8E8A76ED395');
        $this->addSql('DROP TABLE transaction_user');
    }
}
