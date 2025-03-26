<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250324211913 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY FK_62809DB04584665A');
        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY FK_62809DB09395C3F3');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A4584665A');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A9395C3F3');
        $this->addSql('ALTER TABLE customers DROP FOREIGN KEY FK_62534E21F5B7AF75');
        $this->addSql('ALTER TABLE administrators DROP FOREIGN KEY FK_73A716FF5B7AF75');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE2B5CA896');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE9395C3F3');
        $this->addSql('ALTER TABLE wishlist_items DROP FOREIGN KEY FK_B5BB81B54584665A');
        $this->addSql('ALTER TABLE wishlist_items DROP FOREIGN KEY FK_B5BB81B59395C3F3');
        $this->addSql('DROP TABLE order_items');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE customers');
        $this->addSql('DROP TABLE administrators');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE receipts');
        $this->addSql('DROP TABLE wishlist_items');
        $this->addSql('ALTER TABLE addresses ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE inventories ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE products ADD rating NUMERIC(2, 1) DEFAULT 0 NOT NULL, DROP avg_rating, CHANGE unit_price_in_cents price_in_cents INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE order_items (id INT AUTO_INCREMENT NOT NULL, quantity INT NOT NULL, total_price_cents INT NOT NULL, customer_id INT DEFAULT NULL, product_id INT DEFAULT NULL, INDEX IDX_62809DB09395C3F3 (customer_id), INDEX IDX_62809DB04584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, timestamp DATETIME NOT NULL, content VARCHAR(1000) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, rating NUMERIC(2, 1) NOT NULL, customer_id INT DEFAULT NULL, product_id INT DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_5F9E962A9395C3F3 (customer_id), INDEX IDX_5F9E962A4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE customers (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, middle_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, last_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, email VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, address_id INT DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, phone_number VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, UNIQUE INDEX UNIQ_62534E21F5B7AF75 (address_id), UNIQUE INDEX UNIQ_62534E21E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE administrators (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, middle_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, last_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, email VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, ssn VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, address_id INT DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, phone_number VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, UNIQUE INDEX UNIQ_73A716FF5B7AF75 (address_id), UNIQUE INDEX UNIQ_73A716FE7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, date_created DATETIME NOT NULL, date_delivered DATETIME NOT NULL, status VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, total_cents INT NOT NULL, customer_id INT DEFAULT NULL, receipt_id INT DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_E52FFDEE2B5CA896 (receipt_id), INDEX IDX_E52FFDEE9395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE receipts (id INT AUTO_INCREMENT NOT NULL, file_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE wishlist_items (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, customer_id INT DEFAULT NULL, INDEX IDX_B5BB81B59395C3F3 (customer_id), INDEX IDX_B5BB81B54584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB04584665A FOREIGN KEY (product_id) REFERENCES products (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB09395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A4584665A FOREIGN KEY (product_id) REFERENCES products (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A9395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('ALTER TABLE customers ADD CONSTRAINT FK_62534E21F5B7AF75 FOREIGN KEY (address_id) REFERENCES addresses (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE administrators ADD CONSTRAINT FK_73A716FF5B7AF75 FOREIGN KEY (address_id) REFERENCES addresses (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE2B5CA896 FOREIGN KEY (receipt_id) REFERENCES receipts (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE9395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE wishlist_items ADD CONSTRAINT FK_B5BB81B54584665A FOREIGN KEY (product_id) REFERENCES products (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wishlist_items ADD CONSTRAINT FK_B5BB81B59395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE inventories DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE products ADD avg_rating NUMERIC(2, 1) DEFAULT \'0.0\' NOT NULL, DROP rating, CHANGE price_in_cents unit_price_in_cents INT NOT NULL');
        $this->addSql('ALTER TABLE addresses DROP created_at, DROP updated_at');
    }
}
