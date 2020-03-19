<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200319185031 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE blocked_match_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE blocked_match (id BIGINT NOT NULL, helper_id BIGINT NOT NULL, owner_uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CD1A84F9D7693E95 ON blocked_match (helper_id)');
        $this->addSql('COMMENT ON COLUMN blocked_match.owner_uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE blocked_match ADD CONSTRAINT FK_CD1A84F9D7693E95 FOREIGN KEY (helper_id) REFERENCES helpers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE blocked_match_id_seq CASCADE');
        $this->addSql('DROP TABLE blocked_match');
    }
}
