<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241222173650 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de la table categorie et de la relation avec articles.';
    }

    public function up(Schema $schema): void
    {
        // Création de la table catégorie
        $this->addSql('CREATE TABLE categorie (id SERIAL NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        
        // Ajout de la colonne categorie_id avec DEFAULT NULL
        $this->addSql('ALTER TABLE articles ADD categorie_id INT DEFAULT NULL');
        
        // Associer une catégorie par défaut aux articles existants
        $this->addSql('INSERT INTO categorie (nom) VALUES (\'Catégorie par défaut\')');
        $this->addSql('UPDATE articles SET categorie_id = 1 WHERE categorie_id IS NULL');
        
        // Rendre la colonne categorie_id NON NULLABLE
        $this->addSql('ALTER TABLE articles ALTER COLUMN categorie_id SET NOT NULL');
        
        // Ajout de la contrainte de clé étrangère
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        
        // Ajout de l'index pour categorie_id
        $this->addSql('CREATE INDEX IDX_BFDD3168BCF5E72D ON articles (categorie_id)');
    }

    public function down(Schema $schema): void
    {
        // Supprimer la relation et la table catégorie
        $this->addSql('ALTER TABLE articles DROP CONSTRAINT FK_BFDD3168BCF5E72D');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP INDEX IDX_BFDD3168BCF5E72D');
        $this->addSql('ALTER TABLE articles DROP categorie_id');
    }
}
