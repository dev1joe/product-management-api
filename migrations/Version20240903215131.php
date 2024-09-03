<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240903215131 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categories ADD image VARCHAR(255) NOT NULL, CHANGE product_count product_count INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE products CHANGE avg_rating avg_rating NUMERIC(2, 1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE products CHANGE avg_rating avg_rating NUMERIC(2, 1) DEFAULT \'0.0\' NOT NULL');
        $this->addSql('ALTER TABLE categories DROP image, CHANGE product_count product_count INT NOT NULL');
    }
}
