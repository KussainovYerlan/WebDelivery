<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190402133834 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE seller_requests (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, resume LONGTEXT DEFAULT NULL, file VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE seller_requests ADD seller_id INT NOT NULL, ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE seller_requests ADD CONSTRAINT FK_11EEF81A8DE820D9 FOREIGN KEY (seller_id) REFERENCES seller (id)');
        $this->addSql('ALTER TABLE seller_requests ADD CONSTRAINT FK_11EEF81AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_11EEF81A8DE820D9 ON seller_requests (seller_id)');
        $this->addSql('CREATE INDEX IDX_11EEF81AA76ED395 ON seller_requests (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE seller_requests DROP FOREIGN KEY FK_11EEF81A8DE820D9');
        $this->addSql('ALTER TABLE seller_requests DROP FOREIGN KEY FK_11EEF81AA76ED395');
        $this->addSql('DROP INDEX IDX_11EEF81A8DE820D9 ON seller_requests');
        $this->addSql('DROP INDEX IDX_11EEF81AA76ED395 ON seller_requests');
        $this->addSql('ALTER TABLE seller_requests DROP seller_id, DROP user_id');
        $this->addSql('DROP TABLE seller_requests');
    }
}
