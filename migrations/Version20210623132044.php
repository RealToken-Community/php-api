<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210623132044 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tokenlist_integrity (id INT AUTO_INCREMENT NOT NULL, network_id INT DEFAULT NULL, timestamp DATETIME NOT NULL, version_major INT NOT NULL, version_minor INT NOT NULL, version_patch INT NOT NULL, hash VARCHAR(32) NOT NULL, data LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_B0498FBA34128B91 (network_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tokenlist_refer (id INT AUTO_INCREMENT NOT NULL, integrity_types_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, url VARCHAR(150) NOT NULL, INDEX IDX_43D9C3BAB463E39B (integrity_types_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tokenlist_integrity ADD CONSTRAINT FK_B0498FBA34128B91 FOREIGN KEY (network_id) REFERENCES tokenlist_network (id)');
        $this->addSql('ALTER TABLE tokenlist_refer ADD CONSTRAINT FK_43D9C3BAB463E39B FOREIGN KEY (integrity_types_id) REFERENCES tokenlist_integrity (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tokenlist_refer DROP FOREIGN KEY FK_43D9C3BAB463E39B');
        $this->addSql('DROP TABLE tokenlist_integrity');
        $this->addSql('DROP TABLE tokenlist_refer');
    }
}
