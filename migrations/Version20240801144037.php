<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240801144037 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE addresses (id INT AUTO_INCREMENT NOT NULL, country VARCHAR(255) NOT NULL, province VARCHAR(255) NOT NULL, district VARCHAR(255) NOT NULL, street VARCHAR(255) NOT NULL, building VARCHAR(255) NOT NULL, floor INT NOT NULL, apartment INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE administrators (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, middle_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, ssn VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, product_count INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, customer VARCHAR(255) DEFAULT NULL, product VARCHAR(255) NOT NULL, timestamp DATETIME NOT NULL, content VARCHAR(1000) NOT NULL, rating NUMERIC(2, 1) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE customers (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, middle_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE inventories (id INT AUTO_INCREMENT NOT NULL, warehouse VARCHAR(255) NOT NULL, product VARCHAR(255) NOT NULL, quantity INT NOT NULL, last_restock DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE order_items (id INT AUTO_INCREMENT NOT NULL, customer VARCHAR(255) NOT NULL, product VARCHAR(255) NOT NULL, quantity INT NOT NULL, total_price_cents INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, customer VARCHAR(255) NOT NULL, receipt VARCHAR(255) NOT NULL, date_created DATETIME NOT NULL, date_delivered DATETIME NOT NULL, status VARCHAR(255) NOT NULL, total_cents INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(1000) NOT NULL, category VARCHAR(255) NOT NULL, photo VARCHAR(255) NOT NULL, unit_price_cents INT NOT NULL, avg_rating NUMERIC(2, 1) DEFAULT 0 NOT NULL, PRIMARY KEY(id))');
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
