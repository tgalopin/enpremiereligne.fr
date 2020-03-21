<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200321230801 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE helpers ADD accept_vulnerable BOOLEAN DEFAULT TRUE');
        $this->addSql('ALTER TABLE help_requests ADD cc_email VARCHAR(200) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE helpers DROP accept_vulnerable');
        $this->addSql('ALTER TABLE help_requests DROP cc_email');
    }
}
