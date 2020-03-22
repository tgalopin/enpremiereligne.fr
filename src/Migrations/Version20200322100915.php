<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200322100915 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE help_requests ADD cc_first_name VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE help_requests ADD cc_last_name VARCHAR(100) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE help_requests DROP cc_first_name');
        $this->addSql('ALTER TABLE help_requests DROP cc_last_name');
    }
}
