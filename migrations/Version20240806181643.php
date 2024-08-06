<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240806181643 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE administrators ADD address_id INT DEFAULT NULL, DROP address');
        $this->addSql('ALTER TABLE administrators ADD CONSTRAINT FK_73A716FF5B7AF75 FOREIGN KEY (address_id) REFERENCES addresses (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_73A716FF5B7AF75 ON administrators (address_id)');
        $this->addSql('ALTER TABLE comments ADD customer_id INT DEFAULT NULL, ADD product_id INT DEFAULT NULL, DROP customer, DROP product');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A9395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A4584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_5F9E962A9395C3F3 ON comments (customer_id)');
        $this->addSql('CREATE INDEX IDX_5F9E962A4584665A ON comments (product_id)');
        $this->addSql('ALTER TABLE customers ADD address_id INT DEFAULT NULL, DROP address');
        $this->addSql('ALTER TABLE customers ADD CONSTRAINT FK_62534E21F5B7AF75 FOREIGN KEY (address_id) REFERENCES addresses (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_62534E21F5B7AF75 ON customers (address_id)');
        $this->addSql('ALTER TABLE inventories ADD warehouse_id INT DEFAULT NULL, ADD product_id INT DEFAULT NULL, DROP warehouse, DROP product');
        $this->addSql('ALTER TABLE inventories ADD CONSTRAINT FK_936C863D5080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouses (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE inventories ADD CONSTRAINT FK_936C863D4584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_936C863D5080ECDE ON inventories (warehouse_id)');
        $this->addSql('CREATE INDEX IDX_936C863D4584665A ON inventories (product_id)');
        $this->addSql('ALTER TABLE order_items ADD customer_id INT DEFAULT NULL, ADD product_id INT DEFAULT NULL, DROP customer, DROP product');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB09395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB04584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_62809DB09395C3F3 ON order_items (customer_id)');
        $this->addSql('CREATE INDEX IDX_62809DB04584665A ON order_items (product_id)');
        $this->addSql('ALTER TABLE orders ADD customer_id INT DEFAULT NULL, ADD receipt_id INT DEFAULT NULL, DROP customer, DROP receipt');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE9395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE2B5CA896 FOREIGN KEY (receipt_id) REFERENCES receipts (id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE9395C3F3 ON orders (customer_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E52FFDEE2B5CA896 ON orders (receipt_id)');
        $this->addSql('ALTER TABLE products ADD category_id INT DEFAULT NULL, DROP category, CHANGE avg_rating avg_rating NUMERIC(2, 1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('CREATE INDEX IDX_B3BA5A5A12469DE2 ON products (category_id)');
        $this->addSql('ALTER TABLE warehouses CHANGE address address INT DEFAULT NULL');
        $this->addSql('ALTER TABLE warehouses ADD CONSTRAINT FK_AFE9C2B7D4E6F81 FOREIGN KEY (address) REFERENCES addresses (id)');
        $this->addSql('CREATE INDEX IDX_AFE9C2B7D4E6F81 ON warehouses (address)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE9395C3F3');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE2B5CA896');
        $this->addSql('DROP INDEX IDX_E52FFDEE9395C3F3 ON orders');
        $this->addSql('DROP INDEX UNIQ_E52FFDEE2B5CA896 ON orders');
        $this->addSql('ALTER TABLE orders ADD customer VARCHAR(255) NOT NULL, ADD receipt VARCHAR(255) NOT NULL, DROP customer_id, DROP receipt_id');
        $this->addSql('ALTER TABLE customers DROP FOREIGN KEY FK_62534E21F5B7AF75');
        $this->addSql('DROP INDEX UNIQ_62534E21F5B7AF75 ON customers');
        $this->addSql('ALTER TABLE customers ADD address VARCHAR(255) NOT NULL, DROP address_id');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A9395C3F3');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A4584665A');
        $this->addSql('DROP INDEX IDX_5F9E962A9395C3F3 ON comments');
        $this->addSql('DROP INDEX IDX_5F9E962A4584665A ON comments');
        $this->addSql('ALTER TABLE comments ADD customer VARCHAR(255) DEFAULT NULL, ADD product VARCHAR(255) NOT NULL, DROP customer_id, DROP product_id');
        $this->addSql('ALTER TABLE inventories DROP FOREIGN KEY FK_936C863D5080ECDE');
        $this->addSql('ALTER TABLE inventories DROP FOREIGN KEY FK_936C863D4584665A');
        $this->addSql('DROP INDEX IDX_936C863D5080ECDE ON inventories');
        $this->addSql('DROP INDEX IDX_936C863D4584665A ON inventories');
        $this->addSql('ALTER TABLE inventories ADD warehouse VARCHAR(255) NOT NULL, ADD product VARCHAR(255) NOT NULL, DROP warehouse_id, DROP product_id');
        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY FK_62809DB09395C3F3');
        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY FK_62809DB04584665A');
        $this->addSql('DROP INDEX IDX_62809DB09395C3F3 ON order_items');
        $this->addSql('DROP INDEX IDX_62809DB04584665A ON order_items');
        $this->addSql('ALTER TABLE order_items ADD customer VARCHAR(255) NOT NULL, ADD product VARCHAR(255) NOT NULL, DROP customer_id, DROP product_id');
        $this->addSql('ALTER TABLE administrators DROP FOREIGN KEY FK_73A716FF5B7AF75');
        $this->addSql('DROP INDEX UNIQ_73A716FF5B7AF75 ON administrators');
        $this->addSql('ALTER TABLE administrators ADD address VARCHAR(255) NOT NULL, DROP address_id');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5A12469DE2');
        $this->addSql('DROP INDEX IDX_B3BA5A5A12469DE2 ON products');
        $this->addSql('ALTER TABLE products ADD category VARCHAR(255) NOT NULL, DROP category_id, CHANGE avg_rating avg_rating NUMERIC(2, 1) DEFAULT \'0.0\' NOT NULL');
        $this->addSql('ALTER TABLE warehouses DROP FOREIGN KEY FK_AFE9C2B7D4E6F81');
        $this->addSql('DROP INDEX IDX_AFE9C2B7D4E6F81 ON warehouses');
        $this->addSql('ALTER TABLE warehouses CHANGE address address VARCHAR(255) NOT NULL');
    }
}
