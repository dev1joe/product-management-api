<?php

declare(strict_types=1);

namespace trash;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240801130926 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

     public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE addresses (id INT AUTO_INCREMENT NOT NULL, person VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, governorate VARCHAR(255) NOT NULL, district VARCHAR(255) NOT NULL, street VARCHAR(255) NOT NULL, building VARCHAR(255) NOT NULL, floor INT NOT NULL, apartment INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE administrators (id INT AUTO_INCREMENT NOT NULL, firstName VARCHAR(255) NOT NULL, middleName VARCHAR(255) NOT NULL, lastName VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, ssn VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, product_count INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, customer VARCHAR(255) DEFAULT NULL, product VARCHAR(255) NOT NULL, timestamp DATETIME NOT NULL, content VARCHAR(255) NOT NULL, rating DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE customers (id INT AUTO_INCREMENT NOT NULL, firstName VARCHAR(255) NOT NULL, middleName VARCHAR(255) NOT NULL, lastName VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE inventories (id INT AUTO_INCREMENT NOT NULL, warehouse VARCHAR(255) NOT NULL, product VARCHAR(255) NOT NULL, quantity INT NOT NULL, lastRestock DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE order_items (id INT AUTO_INCREMENT NOT NULL, customer VARCHAR(255) NOT NULL, product VARCHAR(255) NOT NULL, quantity INT NOT NULL, totalPriceCents DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, customer VARCHAR(255) NOT NULL, invoice VARCHAR(255) NOT NULL, dateCreated DATETIME NOT NULL, dateDelivered DATETIME NOT NULL, status VARCHAR(255) NOT NULL, totalCents INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, photo VARCHAR(255) NOT NULL, unit_price INT NOT NULL, avg_rating DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE receipts (id INT AUTO_INCREMENT NOT NULL, file_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE warehouses (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE addresses');
        $this->addSql('DROP TABLE administrators');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE customers');
        $this->addSql('DROP TABLE inventories');
        $this->addSql('DROP TABLE order_items');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE receipts');
        $this->addSql('DROP TABLE warehouses');
    }
}
