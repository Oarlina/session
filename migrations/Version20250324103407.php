<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250324103407 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE program (id INT AUTO_INCREMENT NOT NULL, session_id INT NOT NULL, course_id INT NOT NULL, nb_day INT NOT NULL, INDEX IDX_92ED7784613FECDF (session_id), INDEX IDX_92ED7784591CC992 (course_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE program ADD CONSTRAINT FK_92ED7784613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE program ADD CONSTRAINT FK_92ED7784591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE program DROP FOREIGN KEY FK_92ED7784613FECDF');
        $this->addSql('ALTER TABLE program DROP FOREIGN KEY FK_92ED7784591CC992');
        $this->addSql('DROP TABLE program');
    }
}
