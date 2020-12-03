<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201203213056 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_A45BDDC17BA2F5EB ON application (api_token)');
        $this->addSql('ALTER TABLE tokens ADD total_investment DOUBLE PRECISION DEFAULT NULL, ADD initial_maintenance_reserve DOUBLE PRECISION DEFAULT NULL, ADD underlying_asset_price DOUBLE PRECISION DEFAULT NULL, ADD renovation_reserve DOUBLE PRECISION DEFAULT NULL, ADD property_maintenance_monthly DOUBLE PRECISION DEFAULT NULL, ADD rent_start_day DATE DEFAULT NULL, DROP asset_price, DROP property_maintenance');
        $this->addSql('ALTER TABLE user ADD username VARCHAR(50) NOT NULL, ADD ethereum_address VARCHAR(42) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_A45BDDC17BA2F5EB ON application');
        $this->addSql('ALTER TABLE tokens ADD asset_price DOUBLE PRECISION DEFAULT NULL, ADD property_maintenance DOUBLE PRECISION DEFAULT NULL, DROP total_investment, DROP initial_maintenance_reserve, DROP underlying_asset_price, DROP renovation_reserve, DROP property_maintenance_monthly, DROP rent_start_day');
        $this->addSql('ALTER TABLE user DROP username, DROP ethereum_address');
    }
}
