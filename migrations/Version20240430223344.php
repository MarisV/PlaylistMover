<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240430223344 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user_oauth CHANGE refresh_token refresh_token VARCHAR(512) DEFAULT NULL, CHANGE access_token access_token VARCHAR(512) DEFAULT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user CHANGE created_at created_at DATETIME DEFAULT \'1000-01-01 00:00:00\' NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT \'1000-01-01 00:00:00\' NOT NULL');
        $this->addSql('ALTER TABLE user_oauth CHANGE refresh_token refresh_token VARCHAR(255) DEFAULT NULL, CHANGE access_token access_token VARCHAR(255) DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT \'1000-01-01 00:00:00\' NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT \'1000-01-01 00:00:00\' NOT NULL');
    }
}
