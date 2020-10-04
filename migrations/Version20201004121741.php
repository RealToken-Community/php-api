<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201004121741 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tokens ADD gross_rent_year DOUBLE PRECISION DEFAULT NULL, ADD property_management DOUBLE PRECISION DEFAULT NULL, ADD realt_platform DOUBLE PRECISION DEFAULT NULL, ADD net_rent_month DOUBLE PRECISION DEFAULT NULL, ADD net_rent_year DOUBLE PRECISION DEFAULT NULL, CHANGE gross_rent gross_rent_month DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE tokens ADD net_rent_year_per_token DOUBLE PRECISION DEFAULT NULL, ADD net_rent_month_per_token DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE tokens CHANGE rent_per_token annual_percentage_yield DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tokens ADD gross_rent DOUBLE PRECISION DEFAULT NULL, DROP gross_rent_month, DROP gross_rent_year, DROP property_management, DROP realt_platform, DROP net_rent_month, DROP net_rent_year');
        $this->addSql('ALTER TABLE tokens DROP net_rent_year_per_token, DROP net_rent_month_per_token');
        $this->addSql('ALTER TABLE tokens CHANGE annual_percentage_yield rent_per_token DOUBLE PRECISION DEFAULT NULL');
    }
}
