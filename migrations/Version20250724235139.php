<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250724235139 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE historique (id SERIAL NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE history (id SERIAL NOT NULL, historique_id INT NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_27BA704B6128735E ON history (historique_id)');
        $this->addSql('ALTER TABLE history ADD CONSTRAINT FK_27BA704B6128735E FOREIGN KEY (historique_id) REFERENCES historique (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reservation ADD history_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849551E058452 FOREIGN KEY (history_id) REFERENCES history (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_42C849551E058452 ON reservation (history_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT FK_42C849551E058452');
        $this->addSql('ALTER TABLE history DROP CONSTRAINT FK_27BA704B6128735E');
        $this->addSql('DROP TABLE historique');
        $this->addSql('DROP TABLE history');
        $this->addSql('DROP INDEX IDX_42C849551E058452');
        $this->addSql('ALTER TABLE reservation DROP history_id');
    }
}
