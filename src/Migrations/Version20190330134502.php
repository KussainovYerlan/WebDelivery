<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190330134502 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE delivery_order (id INT AUTO_INCREMENT NOT NULL, seller_id INT NOT NULL, user_id INT NOT NULL, address VARCHAR(255) NOT NULL, status INT NOT NULL, INDEX IDX_E522750A8DE820D9 (seller_id), INDEX IDX_E522750AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE delivery_order_product (delivery_order_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_22C2EA06ECFE8C54 (delivery_order_id), INDEX IDX_22C2EA064584665A (product_id), PRIMARY KEY(delivery_order_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE delivery_order ADD CONSTRAINT FK_E522750A8DE820D9 FOREIGN KEY (seller_id) REFERENCES seller (id)');
        $this->addSql('ALTER TABLE delivery_order ADD CONSTRAINT FK_E522750AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE delivery_order_product ADD CONSTRAINT FK_22C2EA06ECFE8C54 FOREIGN KEY (delivery_order_id) REFERENCES delivery_order (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE delivery_order_product ADD CONSTRAINT FK_22C2EA064584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE delivery_order_product DROP FOREIGN KEY FK_22C2EA06ECFE8C54');
        $this->addSql('DROP TABLE delivery_order');
        $this->addSql('DROP TABLE delivery_order_product');
    }
}
