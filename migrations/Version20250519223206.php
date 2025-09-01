<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250519223206 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE application (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, api_token VARCHAR(255) DEFAULT NULL, referer VARCHAR(255) DEFAULT NULL, user_id INT NOT NULL, quota_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_A45BDDC17BA2F5EB (api_token), INDEX IDX_A45BDDC1A76ED395 (user_id), UNIQUE INDEX UNIQ_A45BDDC154E2C62F (quota_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE cache_items (item_id VARBINARY(255) NOT NULL, item_data MEDIUMBLOB NOT NULL, item_lifetime INT UNSIGNED DEFAULT NULL, item_time INT UNSIGNED NOT NULL, PRIMARY KEY(item_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE quota (id INT AUTO_INCREMENT NOT NULL, increment INT DEFAULT 0 NOT NULL, application_id INT NOT NULL, UNIQUE INDEX UNIQ_6C1C0FED3E030ACD (application_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE quota_configuration (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, limitation INT NOT NULL, interval_number INT NOT NULL, interval_type VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE quota_history (id INT AUTO_INCREMENT NOT NULL, access_time DATETIME NOT NULL, quota_id INT NOT NULL, INDEX IDX_95B989C354E2C62F (quota_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE quota_limitations (id INT AUTO_INCREMENT NOT NULL, role VARCHAR(50) NOT NULL, limit_per_minute INT NOT NULL, limit_per_hour INT NOT NULL, limit_per_day INT NOT NULL, limit_per_week INT NOT NULL, limit_per_month INT NOT NULL, limit_per_year INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE token_mapping (id INT AUTO_INCREMENT NOT NULL, source_name VARCHAR(100) NOT NULL, destination_name VARCHAR(100) NOT NULL, last_update DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE tokenlist_integrity (id INT AUTO_INCREMENT NOT NULL, timestamp DATETIME NOT NULL, version_major INT NOT NULL, version_minor INT NOT NULL, version_patch INT NOT NULL, hash VARCHAR(32) NOT NULL, data JSON NOT NULL, network_id INT DEFAULT NULL, INDEX IDX_B0498FBA34128B91 (network_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE tokenlist_network (id INT AUTO_INCREMENT NOT NULL, chain_id INT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE tokenlist_refer (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, url VARCHAR(150) NOT NULL, integrity_types_id INT DEFAULT NULL, INDEX IDX_43D9C3BAB463E39B (integrity_types_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE tokenlist_tag (id INT AUTO_INCREMENT NOT NULL, tag_key VARCHAR(100) NOT NULL, name VARCHAR(100) NOT NULL, description VARCHAR(150) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE tokenlist_token (id INT AUTO_INCREMENT NOT NULL, address VARCHAR(42) NOT NULL, name VARCHAR(100) NOT NULL, symbol VARCHAR(50) NOT NULL, decimals INT NOT NULL, tags JSON DEFAULT NULL, chain_id INT NOT NULL, INDEX IDX_BFFD1962966C2F62 (chain_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE tokens (id INT AUTO_INCREMENT NOT NULL, full_name VARCHAR(100) DEFAULT NULL, short_name VARCHAR(50) NOT NULL, token_price DOUBLE PRECISION DEFAULT NULL, canal VARCHAR(50) NOT NULL, currency VARCHAR(3) DEFAULT NULL, total_tokens INT NOT NULL, ethereum_contract VARCHAR(42) DEFAULT NULL, x_dai_contract VARCHAR(42) DEFAULT NULL, gnosis_contract VARCHAR(42) DEFAULT NULL, goerli_contract VARCHAR(42) DEFAULT NULL, total_investment DOUBLE PRECISION DEFAULT NULL, gross_rent_month DOUBLE PRECISION DEFAULT NULL, annual_percentage_yield DOUBLE PRECISION DEFAULT NULL, property_management_percent DOUBLE PRECISION DEFAULT NULL, realt_platform_percent DOUBLE PRECISION DEFAULT NULL, insurance DOUBLE PRECISION DEFAULT NULL, property_taxes DOUBLE PRECISION DEFAULT NULL, utilities DOUBLE PRECISION DEFAULT NULL, initial_maintenance_reserve DOUBLE PRECISION DEFAULT NULL, coordinate JSON DEFAULT NULL, marketplace_link VARCHAR(255) DEFAULT NULL, image_link JSON DEFAULT NULL, property_type INT DEFAULT NULL, property_type_name VARCHAR(50) DEFAULT NULL, square_feet INT DEFAULT NULL, lot_size INT DEFAULT NULL, bedroom_bath VARCHAR(100) DEFAULT NULL, has_tenants TINYINT(1) DEFAULT NULL, term_of_lease VARCHAR(10) DEFAULT NULL, renewal_date DATETIME DEFAULT NULL, section8paid INT DEFAULT NULL, sell_property_to VARCHAR(50) DEFAULT NULL, gross_rent_year DOUBLE PRECISION DEFAULT NULL, property_management DOUBLE PRECISION DEFAULT NULL, realt_platform DOUBLE PRECISION DEFAULT NULL, net_rent_month DOUBLE PRECISION DEFAULT NULL, net_rent_year DOUBLE PRECISION DEFAULT NULL, net_rent_year_per_token DOUBLE PRECISION DEFAULT NULL, net_rent_month_per_token DOUBLE PRECISION DEFAULT NULL, last_update DATETIME NOT NULL, net_rent_day DOUBLE PRECISION DEFAULT NULL, net_rent_day_per_token DOUBLE PRECISION DEFAULT NULL, rented_units INT DEFAULT NULL, total_units INT DEFAULT NULL, secondary_marketplace JSON DEFAULT NULL, secondary_marketplaces JSON DEFAULT NULL, symbol VARCHAR(100) DEFAULT NULL, blockchain_addresses JSON DEFAULT NULL, underlying_asset_price DOUBLE PRECISION DEFAULT NULL, renovation_reserve DOUBLE PRECISION DEFAULT NULL, property_maintenance_monthly DOUBLE PRECISION DEFAULT NULL, rent_start_date DATE DEFAULT NULL, origin_secondary_marketplaces JSON DEFAULT NULL, initial_launch_date DATE DEFAULT NULL, series_number INT DEFAULT NULL, construction_year INT DEFAULT NULL, construction_type VARCHAR(50) DEFAULT NULL, roof_type VARCHAR(100) DEFAULT NULL, asset_parking VARCHAR(100) DEFAULT NULL, foundation VARCHAR(100) DEFAULT NULL, heating VARCHAR(100) DEFAULT NULL, cooling VARCHAR(100) DEFAULT NULL, token_id_rules INT DEFAULT NULL, rent_calculation_type VARCHAR(20) DEFAULT NULL, realt_listing_fee_percent DOUBLE PRECISION DEFAULT NULL, realt_listing_fee DOUBLE PRECISION DEFAULT NULL, miscellaneous_costs DOUBLE PRECISION DEFAULT NULL, property_stories INT DEFAULT NULL, uuid VARCHAR(42) NOT NULL, total_tokens_reg_summed INT DEFAULT NULL, rental_type VARCHAR(50) DEFAULT NULL, subsidy_status VARCHAR(100) DEFAULT NULL, subsidy_status_value DOUBLE PRECISION DEFAULT NULL, subsidy_by VARCHAR(100) DEFAULT NULL, product_type VARCHAR(100) DEFAULT NULL, neighborhood VARCHAR(100) DEFAULT NULL, UNIQUE INDEX UNIQ_AA5A118ED17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(50) NOT NULL, ethereum_address VARCHAR(42) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE application ADD CONSTRAINT FK_A45BDDC1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE application ADD CONSTRAINT FK_A45BDDC154E2C62F FOREIGN KEY (quota_id) REFERENCES quota (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quota ADD CONSTRAINT FK_6C1C0FED3E030ACD FOREIGN KEY (application_id) REFERENCES application (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quota_history ADD CONSTRAINT FK_95B989C354E2C62F FOREIGN KEY (quota_id) REFERENCES quota (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tokenlist_integrity ADD CONSTRAINT FK_B0498FBA34128B91 FOREIGN KEY (network_id) REFERENCES tokenlist_network (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tokenlist_refer ADD CONSTRAINT FK_43D9C3BAB463E39B FOREIGN KEY (integrity_types_id) REFERENCES tokenlist_integrity (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tokenlist_token ADD CONSTRAINT FK_BFFD1962966C2F62 FOREIGN KEY (chain_id) REFERENCES tokenlist_network (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC1A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC154E2C62F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quota DROP FOREIGN KEY FK_6C1C0FED3E030ACD
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quota_history DROP FOREIGN KEY FK_95B989C354E2C62F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tokenlist_integrity DROP FOREIGN KEY FK_B0498FBA34128B91
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tokenlist_refer DROP FOREIGN KEY FK_43D9C3BAB463E39B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tokenlist_token DROP FOREIGN KEY FK_BFFD1962966C2F62
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE application
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE cache_items
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE quota
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE quota_configuration
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE quota_history
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE quota_limitations
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE token_mapping
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE tokenlist_integrity
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE tokenlist_network
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE tokenlist_refer
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE tokenlist_tag
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE tokenlist_token
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE tokens
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
    }
}
