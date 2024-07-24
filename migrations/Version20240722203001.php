<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240722203001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, word VARCHAR(255) NOT NULL, status INT NOT NULL, attempts INT DEFAULT NULL, max_attempts INT NOT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE guess (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, user_id INT DEFAULT NULL, guess VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_32D30F96E48FD905 (game_id), INDEX IDX_32D30F96A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, team_id INT DEFAULT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', google VARCHAR(255) DEFAULT NULL, INDEX IDX_8D93D649296CD8AE (team_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE guess ADD CONSTRAINT FK_32D30F96E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE guess ADD CONSTRAINT FK_32D30F96A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE guess DROP FOREIGN KEY FK_32D30F96E48FD905');
        $this->addSql('ALTER TABLE guess DROP FOREIGN KEY FK_32D30F96A76ED395');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649296CD8AE');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE guess');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE user');
    }
}
