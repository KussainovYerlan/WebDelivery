<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190409070433 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE checkout_product ADD id INT AUTO_INCREMENT NOT NULL, ADD count INT NOT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE checkout_product ADD CONSTRAINT FK_2F21E0D4146D8724 FOREIGN KEY (checkout_id) REFERENCES checkout (id)');
        $this->addSql('ALTER TABLE checkout_product ADD CONSTRAINT FK_2F21E0D44584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EF25D157E7927C74 ON admin_requests (email)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_EF25D157E7927C74 ON admin_requests');
        $this->addSql('ALTER TABLE checkout_product MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE checkout_product DROP FOREIGN KEY FK_2F21E0D4146D8724');
        $this->addSql('ALTER TABLE checkout_product DROP FOREIGN KEY FK_2F21E0D44584665A');
        $this->addSql('ALTER TABLE checkout_product DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE checkout_product DROP id, DROP count');
    }
}
