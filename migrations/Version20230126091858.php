<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230126091858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_C53D045F989D9B62 ON image');
        $this->addSql('ALTER TABLE image DROP slug');
        $this->addSql('DROP INDEX UNIQ_7CC7DA2C989D9B62 ON video');
        $this->addSql('ALTER TABLE video DROP slug');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image ADD slug VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C53D045F989D9B62 ON image (slug)');
        $this->addSql('ALTER TABLE video ADD slug VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7CC7DA2C989D9B62 ON video (slug)');
    }
}
