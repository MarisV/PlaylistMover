<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240502200635 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE playlist ADD provider_id VARCHAR(64) DEFAULT NULL, ADD image_uri VARCHAR(512) DEFAULT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE playlist DROP provider_id, DROP image_uri, CHANGE created_at created_at DATETIME DEFAULT \'1000-01-01 00:00:00\' NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT \'1000-01-01 00:00:00\' NOT NULL');
    }
}
