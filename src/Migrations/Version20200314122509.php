<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200314122509 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE helpers_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE admins_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE help_requests_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE helpers (id BIGINT NOT NULL, uuid UUID NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, email VARCHAR(200) NOT NULL, zip_code VARCHAR(10) NOT NULL, age SMALLINT NOT NULL, have_children BOOLEAN NOT NULL, can_babysit BOOLEAN NOT NULL, babysit_max_children SMALLINT NOT NULL, babysit_age_ranges TEXT DEFAULT NULL, can_buy_groceries BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D0E84BFCD17F50A6 ON helpers (uuid)');
        $this->addSql('COMMENT ON COLUMN helpers.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN helpers.babysit_age_ranges IS \'(DC2Type:simple_array)\'');
        $this->addSql('CREATE TABLE admins (id BIGINT NOT NULL, username VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A2E0150FF85E0677 ON admins (username)');
        $this->addSql('CREATE TABLE help_requests (id BIGINT NOT NULL, matched_with_id BIGINT DEFAULT NULL, uuid UUID NOT NULL, owner_uuid UUID NOT NULL, help_type VARCHAR(20) NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, email VARCHAR(200) NOT NULL, zip_code VARCHAR(10) NOT NULL, job_type VARCHAR(50) NOT NULL, child_age_range VARCHAR(10) DEFAULT NULL, finished BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F4BE5251D17F50A6 ON help_requests (uuid)');
        $this->addSql('CREATE INDEX IDX_F4BE525160144EC0 ON help_requests (matched_with_id)');
        $this->addSql('CREATE INDEX help_requests_owner_idx ON help_requests (owner_uuid)');
        $this->addSql('COMMENT ON COLUMN help_requests.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN help_requests.owner_uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE help_requests ADD CONSTRAINT FK_F4BE525160144EC0 FOREIGN KEY (matched_with_id) REFERENCES helpers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE help_requests DROP CONSTRAINT FK_F4BE525160144EC0');
        $this->addSql('DROP SEQUENCE helpers_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE admins_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE help_requests_id_seq CASCADE');
        $this->addSql('DROP TABLE helpers');
        $this->addSql('DROP TABLE admins');
        $this->addSql('DROP TABLE help_requests');
    }
}
