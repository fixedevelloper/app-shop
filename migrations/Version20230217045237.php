<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230217045237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mouvement_caisse ADD benficiary_id INT DEFAULT NULL, ADD description VARCHAR(255) NOT NULL, ADD status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE mouvement_caisse ADD CONSTRAINT FK_C8E3DDFE7076FE73 FOREIGN KEY (benficiary_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C8E3DDFE7076FE73 ON mouvement_caisse (benficiary_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mouvement_caisse DROP FOREIGN KEY FK_C8E3DDFE7076FE73');
        $this->addSql('DROP INDEX IDX_C8E3DDFE7076FE73 ON mouvement_caisse');
        $this->addSql('ALTER TABLE mouvement_caisse DROP benficiary_id, DROP description, DROP status');
    }
}
