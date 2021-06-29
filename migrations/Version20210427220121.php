<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210427220121 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tokenlist_network (id INT AUTO_INCREMENT NOT NULL, chain_id INT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tokenlist_refer (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, url VARCHAR(150) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tokenlist_tag (id INT AUTO_INCREMENT NOT NULL, tag_key VARCHAR(100) NOT NULL, name VARCHAR(100) NOT NULL, description VARCHAR(150) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tokenlist_token (id INT AUTO_INCREMENT NOT NULL, chain_id INT NOT NULL, address VARCHAR(42) NOT NULL, name VARCHAR(100) NOT NULL, symbol VARCHAR(50) NOT NULL, decimals INT NOT NULL, tags LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_BFFD1962966C2F62 (chain_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tokenlist_token ADD CONSTRAINT FK_BFFD1962966C2F62 FOREIGN KEY (chain_id) REFERENCES tokenlist_network (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tokenlist_token DROP FOREIGN KEY FK_BFFD1962966C2F62');
        $this->addSql('DROP TABLE tokenlist_network');
        $this->addSql('DROP TABLE tokenlist_refer');
        $this->addSql('DROP TABLE tokenlist_tag');
        $this->addSql('DROP TABLE tokenlist_token');
    }
}
