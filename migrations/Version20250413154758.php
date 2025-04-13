<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250413154758 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categories DROP image');
        $this->addSql('ALTER TABLE manufacturers DROP logo');
        $this->addSql('ALTER TABLE products DROP photo');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE products ADD photo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE manufacturers ADD logo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE categories ADD image VARCHAR(255) DEFAULT NULL');
    }
}
