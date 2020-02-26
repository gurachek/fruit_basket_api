<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200225172442 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE item CHANGE type_id type_id INT DEFAULT NULL, CHANGE basket_id basket_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_1F1B251E1BE1FB52 ON item (basket_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E1BE1FB52');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251EC54C8C93');
        $this->addSql('DROP INDEX IDX_1F1B251E1BE1FB52 ON item');
        $this->addSql('DROP INDEX UNIQ_1F1B251EC54C8C93 ON item');
        $this->addSql('ALTER TABLE item CHANGE type_id type_id INT NOT NULL, CHANGE basket_id basket_id INT NOT NULL');
    }
}
