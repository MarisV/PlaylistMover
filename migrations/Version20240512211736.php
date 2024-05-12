<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240512211736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist ADD created_at DATETIME NOT NULL DEFAULT \'1000-01-01 00:00:00\', ADD updated_at DATETIME NOT NULL DEFAULT \'1000-01-01 00:00:00\'');
        $this->addSql('ALTER TABLE track ADD created_at DATETIME NOT NULL DEFAULT \'1000-01-01 00:00:00\', ADD updated_at DATETIME NOT NULL DEFAULT \'1000-01-01 00:00:00\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE track DROP created_at, DROP updated_at');
    }
}
