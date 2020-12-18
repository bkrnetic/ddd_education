<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201218130835 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE articles (category_id UUID DEFAULT NULL, "name" VARCHAR(255) NOT NULL, "content" TEXT NOT NULL, "published" BOOLEAN NOT NULL, "id" UUID NOT NULL, "created_at" TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, "updated_at" TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, "deleted_at" TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY("id"))');
        $this->addSql('CREATE INDEX IDX_BFDD316812469DE2 ON articles (category_id)');
        $this->addSql('CREATE TABLE categories (created_by_user_id UUID DEFAULT NULL, "name" VARCHAR(255) NOT NULL, "id" UUID NOT NULL, "created_at" TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, "updated_at" TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, "deleted_at" TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY("id"))');
        $this->addSql('CREATE INDEX IDX_3AF346687D182D95 ON categories (created_by_user_id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD316812469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_3AF346687D182D95 FOREIGN KEY (created_by_user_id) REFERENCES users (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER INDEX uniq_1483a5e9f85e0677 RENAME TO UNIQ_1483A5E987D0BBC5');
        $this->addSql('ALTER INDEX uniq_1483a5e9e7927c74 RENAME TO UNIQ_1483A5E92461DF58');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE articles DROP CONSTRAINT FK_BFDD316812469DE2');
        $this->addSql('DROP TABLE articles');
        $this->addSql('DROP TABLE categories');
        $this->addSql('ALTER INDEX uniq_1483a5e987d0bbc5 RENAME TO uniq_1483a5e9f85e0677');
        $this->addSql('ALTER INDEX uniq_1483a5e92461df58 RENAME TO uniq_1483a5e9e7927c74');
    }
}
