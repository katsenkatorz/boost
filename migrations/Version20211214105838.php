<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211214105838 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tickets DROP priority_id');
        $this->addSql('ALTER TABLE tickets ADD CONSTRAINT FK_54469DF4C54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('CREATE INDEX IDX_54469DF4C54C8C93 ON tickets (type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tickets DROP FOREIGN KEY FK_54469DF4C54C8C93');
        $this->addSql('DROP INDEX IDX_54469DF4C54C8C93 ON tickets');
        $this->addSql('ALTER TABLE tickets ADD priority_id INT DEFAULT NULL');
    }
}
