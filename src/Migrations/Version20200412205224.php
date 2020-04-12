<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200412205224 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE invitation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE invitation (id INT NOT NULL, email_hash VARCHAR(64) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F11D61A24E8E423D ON invitation (email_hash)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE invitation_id_seq CASCADE');
        $this->addSql('DROP TABLE invitation');
    }
}
