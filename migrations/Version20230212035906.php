<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230212035906 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE seller_shop ADD caisse_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE seller_shop ADD CONSTRAINT FK_AE93894227B4FEBF FOREIGN KEY (caisse_id) REFERENCES caisse (id)');
        $this->addSql('CREATE INDEX IDX_AE93894227B4FEBF ON seller_shop (caisse_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE seller_shop DROP FOREIGN KEY FK_AE93894227B4FEBF');
        $this->addSql('DROP INDEX IDX_AE93894227B4FEBF ON seller_shop');
        $this->addSql('ALTER TABLE seller_shop DROP caisse_id');
    }
}
