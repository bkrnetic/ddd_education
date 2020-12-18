<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200624131140 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Users table creation';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE users (id UUID NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(64) NOT NULL, name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, roles TEXT NOT NULL, is_active BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9F85E0677 ON users (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('COMMENT ON COLUMN "users".roles IS \'(DC2Type:array)\'');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE "users"');
    }
}
