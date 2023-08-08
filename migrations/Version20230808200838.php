<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230808200838 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tokens ADD subsidy_status VARCHAR(100) DEFAULT NULL, ADD subsidy_status_value DOUBLE PRECISION DEFAULT NULL, ADD subsidy_by VARCHAR(100) DEFAULT NULL, CHANGE last_update last_update DATETIME NOT NULL, CHANGE secondary_marketplace secondary_marketplace LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE tokens RENAME INDEX uuid TO UNIQ_AA5A118ED17F50A6');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tokens DROP subsidy_status, DROP subsidy_status_value, DROP subsidy_by, CHANGE last_update last_update DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE secondary_marketplace secondary_marketplace LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE tokens RENAME INDEX uniq_aa5a118ed17f50a6 TO uuid');
    }
}
