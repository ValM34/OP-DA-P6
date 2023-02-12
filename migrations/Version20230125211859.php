<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230125211859 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category ADD slug VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C15E237E06 ON category (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C1989D9B62 ON category (slug)');
        $this->addSql('ALTER TABLE image ADD slug VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C53D045FB548B0F ON image (path)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C53D045F989D9B62 ON image (slug)');
        $this->addSql('ALTER TABLE trick ADD slug VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D8F0A91E989D9B62 ON trick (slug)');
        $this->addSql('ALTER TABLE video ADD slug VARCHAR(255) NOT NULL, CHANGE path path VARCHAR(300) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7CC7DA2CB548B0F ON video (path)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7CC7DA2C989D9B62 ON video (slug)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_64C19C15E237E06 ON category');
        $this->addSql('DROP INDEX UNIQ_64C19C1989D9B62 ON category');
        $this->addSql('ALTER TABLE category DROP slug');
        $this->addSql('DROP INDEX UNIQ_D8F0A91E989D9B62 ON trick');
        $this->addSql('ALTER TABLE trick DROP slug');
        $this->addSql('DROP INDEX UNIQ_C53D045FB548B0F ON image');
        $this->addSql('DROP INDEX UNIQ_C53D045F989D9B62 ON image');
        $this->addSql('ALTER TABLE image DROP slug');
        $this->addSql('DROP INDEX UNIQ_7CC7DA2CB548B0F ON video');
        $this->addSql('DROP INDEX UNIQ_7CC7DA2C989D9B62 ON video');
        $this->addSql('ALTER TABLE video DROP slug, CHANGE path path VARCHAR(2048) NOT NULL');
    }
}
