<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250324102705 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE session_intern (session_id INT NOT NULL, intern_id INT NOT NULL, INDEX IDX_CA12556F613FECDF (session_id), INDEX IDX_CA12556F525DD4B4 (intern_id), PRIMARY KEY(session_id, intern_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE session_intern ADD CONSTRAINT FK_CA12556F613FECDF FOREIGN KEY (session_id) REFERENCES session (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session_intern ADD CONSTRAINT FK_CA12556F525DD4B4 FOREIGN KEY (intern_id) REFERENCES intern (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE session_intern DROP FOREIGN KEY FK_CA12556F613FECDF');
        $this->addSql('ALTER TABLE session_intern DROP FOREIGN KEY FK_CA12556F525DD4B4');
        $this->addSql('DROP TABLE session_intern');
    }
}
