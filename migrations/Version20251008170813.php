<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251008170813 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE location CHANGE latitude latitude NUMERIC(20, 16) DEFAULT NULL, CHANGE longitude longitude NUMERIC(20, 16) DEFAULT NULL');
        $this->addSql('ALTER TABLE registration ADD first_registered_at DATETIME NOT NULL');
        $this->addSql('UPDATE registration SET first_registered_at = registered_at WHERE 1');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE location CHANGE latitude latitude NUMERIC(10, 0) DEFAULT NULL, CHANGE longitude longitude NUMERIC(10, 0) DEFAULT NULL');
        $this->addSql('ALTER TABLE registration DROP first_registered_at');
    }
}
