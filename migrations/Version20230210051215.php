<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230210051215 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, image_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, price DOUBLE PRECISION NOT NULL, status VARCHAR(255) NOT NULL, type VARCHAR(255) DEFAULT NULL, sku VARCHAR(255) DEFAULT NULL, INDEX IDX_23A0E6612469DE2 (category_id), UNIQUE INDEX UNIQ_23A0E663DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE caisse (id INT AUTO_INCREMENT NOT NULL, shop_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, libelle VARCHAR(255) NOT NULL, solde DOUBLE PRECISION NOT NULL, hasretraitespece TINYINT(1) NOT NULL, maxretraitoperation DOUBLE PRECISION NOT NULL, maxretraitperiode DOUBLE PRECISION NOT NULL, INDEX IDX_B2A353C84D16C4DD (shop_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_64C19C13DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, date_created DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, src VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, alt VARCHAR(255) DEFAULT NULL, position INT DEFAULT NULL, date_created DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE journee_comptable (id INT AUTO_INCREMENT NOT NULL, caisse_id INT DEFAULT NULL, soldeouverture DOUBLE PRECISION NOT NULL, datecomptable DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', versement DOUBLE PRECISION NOT NULL, retrait DOUBLE PRECISION NOT NULL, soldetheorique DOUBLE PRECISION NOT NULL, status TINYINT(1) NOT NULL, INDEX IDX_9142FD1827B4FEBF (caisse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE line_sale (id INT AUTO_INCREMENT NOT NULL, article_id INT DEFAULT NULL, sale_article_id INT DEFAULT NULL, quantity INT NOT NULL, price DOUBLE PRECISION NOT NULL, INDEX IDX_6E0633DC7294869C (article_id), INDEX IDX_6E0633DC8FC062D8 (sale_article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mouvement_caisse (id INT AUTO_INCREMENT NOT NULL, caisse_id INT DEFAULT NULL, libelle VARCHAR(255) NOT NULL, debit DOUBLE PRECISION NOT NULL, credit DOUBLE PRECISION NOT NULL, dateoperation DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', datestring VARCHAR(255) NOT NULL, INDEX IDX_C8E3DDFE27B4FEBF (caisse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sale_article (id INT AUTO_INCREMENT NOT NULL, seller_shop_id INT DEFAULT NULL, customer_name VARCHAR(255) NOT NULL, amount_total DOUBLE PRECISION NOT NULL, tax DOUBLE PRECISION DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, status VARCHAR(255) NOT NULL, date_created DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, INDEX IDX_7724CCF496187DCC (seller_shop_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE seller_shop (id INT AUTO_INCREMENT NOT NULL, seller_id INT DEFAULT NULL, shop_id INT DEFAULT NULL, is_activate TINYINT(1) NOT NULL, solde DOUBLE PRECISION NOT NULL, totalsell DOUBLE PRECISION NOT NULL, INDEX IDX_AE9389428DE820D9 (seller_id), INDEX IDX_AE9389424D16C4DD (shop_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session_caisse (id INT AUTO_INCREMENT NOT NULL, seller_shop_id INT DEFAULT NULL, caisse_id INT DEFAULT NULL, codecaisse VARCHAR(255) NOT NULL, totalcaisse DOUBLE PRECISION NOT NULL, restecaisse DOUBLE PRECISION NOT NULL, active TINYINT(1) NOT NULL, date_start DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_end DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_DDC8599196187DCC (seller_shop_id), INDEX IDX_DDC8599127B4FEBF (caisse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shop (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock (id INT AUTO_INCREMENT NOT NULL, article_id INT DEFAULT NULL, shop_id INT DEFAULT NULL, quantity INT NOT NULL, lastquantity INT NOT NULL, date_created DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, INDEX IDX_4B3656607294869C (article_id), INDEX IDX_4B3656604D16C4DD (shop_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, roles JSON DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, isactivate TINYINT(1) DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E6612469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E663DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE caisse ADD CONSTRAINT FK_B2A353C84D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (id)');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C13DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE journee_comptable ADD CONSTRAINT FK_9142FD1827B4FEBF FOREIGN KEY (caisse_id) REFERENCES caisse (id)');
        $this->addSql('ALTER TABLE line_sale ADD CONSTRAINT FK_6E0633DC7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE line_sale ADD CONSTRAINT FK_6E0633DC8FC062D8 FOREIGN KEY (sale_article_id) REFERENCES sale_article (id)');
        $this->addSql('ALTER TABLE mouvement_caisse ADD CONSTRAINT FK_C8E3DDFE27B4FEBF FOREIGN KEY (caisse_id) REFERENCES caisse (id)');
        $this->addSql('ALTER TABLE sale_article ADD CONSTRAINT FK_7724CCF496187DCC FOREIGN KEY (seller_shop_id) REFERENCES seller_shop (id)');
        $this->addSql('ALTER TABLE seller_shop ADD CONSTRAINT FK_AE9389428DE820D9 FOREIGN KEY (seller_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE seller_shop ADD CONSTRAINT FK_AE9389424D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (id)');
        $this->addSql('ALTER TABLE session_caisse ADD CONSTRAINT FK_DDC8599196187DCC FOREIGN KEY (seller_shop_id) REFERENCES seller_shop (id)');
        $this->addSql('ALTER TABLE session_caisse ADD CONSTRAINT FK_DDC8599127B4FEBF FOREIGN KEY (caisse_id) REFERENCES caisse (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B3656607294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B3656604D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E6612469DE2');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E663DA5256D');
        $this->addSql('ALTER TABLE caisse DROP FOREIGN KEY FK_B2A353C84D16C4DD');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C13DA5256D');
        $this->addSql('ALTER TABLE journee_comptable DROP FOREIGN KEY FK_9142FD1827B4FEBF');
        $this->addSql('ALTER TABLE line_sale DROP FOREIGN KEY FK_6E0633DC7294869C');
        $this->addSql('ALTER TABLE line_sale DROP FOREIGN KEY FK_6E0633DC8FC062D8');
        $this->addSql('ALTER TABLE mouvement_caisse DROP FOREIGN KEY FK_C8E3DDFE27B4FEBF');
        $this->addSql('ALTER TABLE sale_article DROP FOREIGN KEY FK_7724CCF496187DCC');
        $this->addSql('ALTER TABLE seller_shop DROP FOREIGN KEY FK_AE9389428DE820D9');
        $this->addSql('ALTER TABLE seller_shop DROP FOREIGN KEY FK_AE9389424D16C4DD');
        $this->addSql('ALTER TABLE session_caisse DROP FOREIGN KEY FK_DDC8599196187DCC');
        $this->addSql('ALTER TABLE session_caisse DROP FOREIGN KEY FK_DDC8599127B4FEBF');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B3656607294869C');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B3656604D16C4DD');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE caisse');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE journee_comptable');
        $this->addSql('DROP TABLE line_sale');
        $this->addSql('DROP TABLE mouvement_caisse');
        $this->addSql('DROP TABLE sale_article');
        $this->addSql('DROP TABLE seller_shop');
        $this->addSql('DROP TABLE session_caisse');
        $this->addSql('DROP TABLE shop');
        $this->addSql('DROP TABLE stock');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
