<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201107123224 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tokens ADD rented_units INT DEFAULT NULL, ADD total_units INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tokens ADD secondary_marketplace LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE tokens DROP public_sale, DROP on_uniswap');

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tokens DROP rented_units, DROP total_units');
        $this->addSql('ALTER TABLE tokens DROP secondary_marketplace');
        $this->addSql('ALTER TABLE tokens ADD public_sale VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD on_uniswap TINYINT(1) DEFAULT NULL');
    }
}
