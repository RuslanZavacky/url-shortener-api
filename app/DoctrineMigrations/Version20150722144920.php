<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150722144920 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE url (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(4000) NOT NULL, original_url VARCHAR(4000) NOT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', query_param LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', code VARCHAR(128) DEFAULT NULL, short_url VARCHAR(200) DEFAULT NULL, sequence INT NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, redirect_count INT NOT NULL, unique_redirect_count INT NOT NULL, last_redirect_on DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_F47645AE77153098 (code), UNIQUE INDEX UNIQ_F47645AE83360531 (short_url), INDEX code_idx (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE url_stat (id INT AUTO_INCREMENT NOT NULL, url_id INT DEFAULT NULL, ip VARCHAR(100) NOT NULL, user_agent VARCHAR(2000) DEFAULT NULL, created DATETIME NOT NULL, INDEX IDX_3EEC369981CFDAE7 (url_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `transaction` (transaction_id VARCHAR(255) NOT NULL, model VARCHAR(255) NOT NULL, related_ids LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', related_route VARCHAR(255) NOT NULL, method VARCHAR(7) NOT NULL, request_source VARCHAR(255) NOT NULL, request_id VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, status SMALLINT NOT NULL, success TINYINT(1) NOT NULL, message LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', PRIMARY KEY(transaction_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE url_stat ADD CONSTRAINT FK_3EEC369981CFDAE7 FOREIGN KEY (url_id) REFERENCES url (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE url_stat DROP FOREIGN KEY FK_3EEC369981CFDAE7');
        $this->addSql('DROP TABLE url');
        $this->addSql('DROP TABLE url_stat');
        $this->addSql('DROP TABLE `transaction`');
    }
}
