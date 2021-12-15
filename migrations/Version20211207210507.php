<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211207210507 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tokens ADD initial_launch_date DATE DEFAULT NULL, ADD series_number INT DEFAULT NULL, ADD construction_year DATE DEFAULT NULL, ADD construction_type VARCHAR(50) DEFAULT NULL, ADD roof_type VARCHAR(50) DEFAULT NULL, ADD asset_parking VARCHAR(50) DEFAULT NULL, ADD foundation VARCHAR(50) DEFAULT NULL, ADD heating VARCHAR(100) DEFAULT NULL, ADD cooling VARCHAR(50) DEFAULT NULL, ADD token_id_rules INT DEFAULT NULL, ADD rent_calculation_type VARCHAR(20) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tokens DROP initial_launch_date, DROP series_number, DROP construction_year, DROP construction_type, DROP roof_type, DROP asset_parking, DROP foundation, DROP heating, DROP cooling, DROP token_id_rules, DROP rent_calculation_type');
    }
}
