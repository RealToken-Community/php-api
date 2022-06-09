<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220608203111 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tokens ADD uuid VARCHAR(42) NOT NULL, CHANGE ethereum_contract ethereum_contract VARCHAR(42) DEFAULT NULL, CHANGE matic_contract gnosis_contract VARCHAR(42) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AA5A118ED17F50A6 ON tokens (uuid)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_AA5A118ED17F50A6 ON tokens');
        $this->addSql('ALTER TABLE tokens DROP uuid, CHANGE ethereum_contract ethereum_contract VARCHAR(42) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE gnosis_contract matic_contract VARCHAR(42) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
