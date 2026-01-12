<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260106234648 extends AbstractMigration
{
	public function getDescription() : string
	{
		return '';
	}

	public function up(Schema $schema) : void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

		$this->addSql('ALTER TABLE tokens ADD renovation_pool_monthly_percent_fee DOUBLE PRECISION DEFAULT NULL, ADD renovation_pool_initial_fee DOUBLE PRECISION DEFAULT NULL, ADD rental_history LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:array)\', ADD token_prices LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:object)\'');
	}

	public function down(Schema $schema) : void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

		$this->addSql('ALTER TABLE tokens DROP renovation_pool_monthly_percent_fee, DROP renovation_pool_initial_fee, DROP rental_history, DROP token_prices');
	}
}