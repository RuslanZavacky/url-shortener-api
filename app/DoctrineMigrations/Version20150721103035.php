<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150721103035 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE url (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(4000) NOT NULL, original_url VARCHAR(4000) NOT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', query_param VARCHAR(2048) DEFAULT NULL, code VARCHAR(128) DEFAULT NULL, short_url VARCHAR(200) DEFAULT NULL, sequence INT NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, UNIQUE INDEX UNIQ_F47645AE77153098 (code), UNIQUE INDEX UNIQ_F47645AE83360531 (short_url), INDEX code_idx (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `transaction` (transaction_id VARCHAR(255) NOT NULL, model VARCHAR(255) NOT NULL, related_ids LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', related_route VARCHAR(255) NOT NULL, method VARCHAR(7) NOT NULL, request_source VARCHAR(255) NOT NULL, request_id VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, status SMALLINT NOT NULL, success TINYINT(1) NOT NULL, message LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', PRIMARY KEY(transaction_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE url');
        $this->addSql('DROP TABLE `transaction`');
    }
}
