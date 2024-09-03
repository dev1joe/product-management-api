<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240903213106 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Manufacturer (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, logo VARCHAR(255) NOT NULL, product_count INT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE wishlist_items (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, customer_id INT DEFAULT NULL, INDEX IDX_B5BB81B54584665A (product_id), INDEX IDX_B5BB81B59395C3F3 (customer_id), PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE wishlist_items ADD CONSTRAINT FK_B5BB81B54584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wishlist_items ADD CONSTRAINT FK_B5BB81B59395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE addresses CHANGE floor floor INT DEFAULT NULL, CHANGE apartment apartment INT DEFAULT NULL');
        $this->addSql('ALTER TABLE administrators ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD deleted_at DATETIME DEFAULT NULL, ADD phone_number VARCHAR(20) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_73A716FE7927C74 ON administrators (email)');
        $this->addSql('ALTER TABLE categories ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE comments ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE customers ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD deleted_at DATETIME DEFAULT NULL, ADD phone_number VARCHAR(20) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_62534E21E7927C74 ON customers (email)');
        $this->addSql('ALTER TABLE orders ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE products ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD deleted_at DATETIME DEFAULT NULL, ADD manufacturer_id INT DEFAULT NULL, CHANGE avg_rating avg_rating NUMERIC(2, 1) DEFAULT 0 NOT NULL, CHANGE unit_price_cents unit_price_in_cents INT NOT NULL');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5AA23B42D FOREIGN KEY (manufacturer_id) REFERENCES Manufacturer (id) ON DELETE RESTRICT');
        $this->addSql('CREATE INDEX IDX_B3BA5A5AA23B42D ON products (manufacturer_id)');
        $this->addSql('ALTER TABLE warehouses DROP FOREIGN KEY FK_AFE9C2B7D4E6F81');
        $this->addSql('DROP INDEX IDX_AFE9C2B7D4E6F81 ON warehouses');
        $this->addSql('ALTER TABLE warehouses ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD deleted_at DATETIME DEFAULT NULL, CHANGE address address_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE warehouses ADD CONSTRAINT FK_AFE9C2B7F5B7AF75 FOREIGN KEY (address_id) REFERENCES addresses (id) ON DELETE RESTRICT');
        $this->addSql('CREATE INDEX IDX_AFE9C2B7F5B7AF75 ON warehouses (address_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE wishlist_items DROP FOREIGN KEY FK_B5BB81B54584665A');
        $this->addSql('ALTER TABLE wishlist_items DROP FOREIGN KEY FK_B5BB81B59395C3F3');
        $this->addSql('DROP TABLE Manufacturer');
        $this->addSql('DROP TABLE wishlist_items');
        $this->addSql('ALTER TABLE orders DROP created_at, DROP updated_at');
        $this->addSql('DROP INDEX UNIQ_62534E21E7927C74 ON customers');
        $this->addSql('ALTER TABLE customers DROP created_at, DROP updated_at, DROP deleted_at, DROP phone_number');
        $this->addSql('ALTER TABLE comments DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE categories DROP created_at, DROP updated_at');
        $this->addSql('DROP INDEX UNIQ_73A716FE7927C74 ON administrators');
        $this->addSql('ALTER TABLE administrators DROP created_at, DROP updated_at, DROP deleted_at, DROP phone_number');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5AA23B42D');
        $this->addSql('DROP INDEX IDX_B3BA5A5AA23B42D ON products');
        $this->addSql('ALTER TABLE products DROP created_at, DROP updated_at, DROP deleted_at, DROP manufacturer_id, CHANGE avg_rating avg_rating NUMERIC(2, 1) DEFAULT \'0.0\' NOT NULL, CHANGE unit_price_in_cents unit_price_cents INT NOT NULL');
        $this->addSql('ALTER TABLE warehouses DROP FOREIGN KEY FK_AFE9C2B7F5B7AF75');
        $this->addSql('DROP INDEX IDX_AFE9C2B7F5B7AF75 ON warehouses');
        $this->addSql('ALTER TABLE warehouses DROP created_at, DROP updated_at, DROP deleted_at, CHANGE address_id address INT DEFAULT NULL');
        $this->addSql('ALTER TABLE warehouses ADD CONSTRAINT FK_AFE9C2B7D4E6F81 FOREIGN KEY (address) REFERENCES addresses (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_AFE9C2B7D4E6F81 ON warehouses (address)');
        $this->addSql('ALTER TABLE addresses CHANGE floor floor INT NOT NULL, CHANGE apartment apartment INT NOT NULL');
    }
}
