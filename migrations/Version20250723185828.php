<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250723185828 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reservation_user (reservation_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(reservation_id, user_id))');
        $this->addSql('CREATE INDEX IDX_9BAA1B21B83297E7 ON reservation_user (reservation_id)');
        $this->addSql('CREATE INDEX IDX_9BAA1B21A76ED395 ON reservation_user (user_id)');
        $this->addSql('ALTER TABLE reservation_user ADD CONSTRAINT FK_9BAA1B21B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reservation_user ADD CONSTRAINT FK_9BAA1B21A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT fk_42c84955fb88e14f');
        $this->addSql('DROP INDEX uniq_42c84955fb88e14f');
        $this->addSql('ALTER TABLE reservation DROP utilisateur_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE reservation_user DROP CONSTRAINT FK_9BAA1B21B83297E7');
        $this->addSql('ALTER TABLE reservation_user DROP CONSTRAINT FK_9BAA1B21A76ED395');
        $this->addSql('DROP TABLE reservation_user');
        $this->addSql('ALTER TABLE reservation ADD utilisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT fk_42c84955fb88e14f FOREIGN KEY (utilisateur_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_42c84955fb88e14f ON reservation (utilisateur_id)');
    }
}
