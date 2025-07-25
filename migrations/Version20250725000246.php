<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250725000246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT fk_42c849551e058452');
        $this->addSql('DROP SEQUENCE history_id_seq CASCADE');
        $this->addSql('ALTER TABLE history DROP CONSTRAINT fk_27ba704b6128735e');
        $this->addSql('DROP TABLE history');
        $this->addSql('ALTER TABLE historique ADD reservation JSON NOT NULL');
        $this->addSql('DROP INDEX idx_42c849551e058452');
        $this->addSql('ALTER TABLE reservation DROP history_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE history_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE history (id SERIAL NOT NULL, historique_id INT NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_27ba704b6128735e ON history (historique_id)');
        $this->addSql('ALTER TABLE history ADD CONSTRAINT fk_27ba704b6128735e FOREIGN KEY (historique_id) REFERENCES historique (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reservation ADD history_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT fk_42c849551e058452 FOREIGN KEY (history_id) REFERENCES history (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_42c849551e058452 ON reservation (history_id)');
        $this->addSql('ALTER TABLE historique DROP reservation');
    }
}
