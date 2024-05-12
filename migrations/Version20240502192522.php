<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240502192522 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE playlist (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, title VARCHAR(512) NOT NULL, provider VARCHAR(50) NOT NULL, provider_uri VARCHAR(512) DEFAULT NULL, created_at DATETIME NOT NULL DEFAULT "1000-01-01 00:00:00", updated_at DATETIME NOT NULL DEFAULT "1000-01-01 00:00:00", INDEX IDX_D782112D7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE playlist ADD CONSTRAINT FK_D782112D7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE playlist DROP FOREIGN KEY FK_D782112D7E3C61F9');
        $this->addSql('DROP TABLE playlist');
    }
}
