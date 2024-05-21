<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240521205716 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE playlist_track DROP INDEX UNIQ_75FFE1E55ED23C43, ADD INDEX IDX_75FFE1E55ED23C43 (track_id)');
        $this->addSql('DROP INDEX track_isrc_idx ON track');
        $this->addSql('CREATE INDEX track_isrc_idx ON track (isrc)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX track_isrc_idx ON track');
        $this->addSql('CREATE INDEX track_isrc_idx ON track (isrc)');
        $this->addSql('ALTER TABLE playlist_track DROP INDEX IDX_75FFE1E55ED23C43, ADD UNIQUE INDEX UNIQ_75FFE1E55ED23C43 (track_id)');
    }
}
