<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260501160529 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE atelier (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(180) NOT NULL, description CLOB NOT NULL, duree_minutes INTEGER NOT NULL, capacite INTEGER NOT NULL, status VARCHAR(20) NOT NULL, archived BOOLEAN NOT NULL, theme_id INTEGER DEFAULT NULL, intervenant_id INTEGER DEFAULT NULL, owner_id INTEGER DEFAULT NULL, CONSTRAINT FK_E1BB182359027487 FOREIGN KEY (theme_id) REFERENCES theme (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_E1BB1823AB9A1716 FOREIGN KEY (intervenant_id) REFERENCES intervenant (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_E1BB18237E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_E1BB182359027487 ON atelier (theme_id)');
        $this->addSql('CREATE INDEX IDX_E1BB1823AB9A1716 ON atelier (intervenant_id)');
        $this->addSql('CREATE INDEX IDX_E1BB18237E3C61F9 ON atelier (owner_id)');
        $this->addSql('CREATE TABLE intervenant (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, full_name VARCHAR(120) NOT NULL)');
        $this->addSql('CREATE TABLE session_atelier (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, date DATETIME NOT NULL, capacite INTEGER NOT NULL, atelier_id INTEGER NOT NULL, CONSTRAINT FK_F3639A7982E2CF35 FOREIGN KEY (atelier_id) REFERENCES atelier (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_F3639A7982E2CF35 ON session_atelier (atelier_id)');
        $this->addSql('CREATE TABLE theme (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(100) NOT NULL)');
        $this->addSql('CREATE TABLE "user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE atelier');
        $this->addSql('DROP TABLE intervenant');
        $this->addSql('DROP TABLE session_atelier');
        $this->addSql('DROP TABLE theme');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
