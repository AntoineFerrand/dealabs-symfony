<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210627211608 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE account (id INT AUTO_INCREMENT NOT NULL, login VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, roles JSON NOT NULL, vote_count INT NOT NULL, deal_count INT NOT NULL, comment_count INT NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE account_badge (account_id INT NOT NULL, badge_id INT NOT NULL, INDEX IDX_C87F916C9B6B5FBA (account_id), INDEX IDX_C87F916CF7A2C2FC (badge_id), PRIMARY KEY(account_id, badge_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE account_deals (account_id INT NOT NULL, deals_id INT NOT NULL, INDEX IDX_D9B65DEA9B6B5FBA (account_id), INDEX IDX_D9B65DEA97ED4789 (deals_id), PRIMARY KEY(account_id, deals_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE alerte (id INT AUTO_INCREMENT NOT NULL, creator_id INT NOT NULL, motscles VARCHAR(255) NOT NULL, temperature INT NOT NULL, notification TINYINT(1) NOT NULL, INDEX IDX_3AE753A61220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE avatar (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, image_name VARCHAR(255) DEFAULT NULL, image_original_name VARCHAR(255) DEFAULT NULL, image_mime_type VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, image_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', UNIQUE INDEX UNIQ_1677722F9B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE badge (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commentaire (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, deal_id INT NOT NULL, content VARCHAR(255) NOT NULL, date DATETIME NOT NULL, INDEX IDX_67F068BC9B6B5FBA (account_id), INDEX IDX_67F068BCF60E2305 (deal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE deals (id INT AUTO_INCREMENT NOT NULL, partenaire_id INT DEFAULT NULL, creator_id INT NOT NULL, shipping DOUBLE PRECISION NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, discount_price DOUBLE PRECISION DEFAULT NULL, normal_price DOUBLE PRECISION DEFAULT NULL, link VARCHAR(255) DEFAULT NULL, expiration_date DATE DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, promo_code VARCHAR(255) DEFAULT NULL, type VARCHAR(4) NOT NULL, creation_date DATETIME NOT NULL, image_name VARCHAR(255) DEFAULT NULL, image_original_name VARCHAR(255) DEFAULT NULL, image_mime_type VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, image_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', INDEX IDX_EF39849B98DE13AC (partenaire_id), INDEX IDX_EF39849B61220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE groupe (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE groupe_deals (groupe_id INT NOT NULL, deals_id INT NOT NULL, INDEX IDX_BA944ACB7A45358C (groupe_id), INDEX IDX_BA944ACB97ED4789 (deals_id), PRIMARY KEY(groupe_id, deals_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partenaire (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE thumb (id INT AUTO_INCREMENT NOT NULL, commentaire_id INT NOT NULL, account_id INT NOT NULL, INDEX IDX_1537D1DBBA9CD190 (commentaire_id), INDEX IDX_1537D1DB9B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vote (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, deals_id INT NOT NULL, is_positive TINYINT(1) NOT NULL, INDEX IDX_5A1085649B6B5FBA (account_id), INDEX IDX_5A10856497ED4789 (deals_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE account_badge ADD CONSTRAINT FK_C87F916C9B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE account_badge ADD CONSTRAINT FK_C87F916CF7A2C2FC FOREIGN KEY (badge_id) REFERENCES badge (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE account_deals ADD CONSTRAINT FK_D9B65DEA9B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE account_deals ADD CONSTRAINT FK_D9B65DEA97ED4789 FOREIGN KEY (deals_id) REFERENCES deals (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE alerte ADD CONSTRAINT FK_3AE753A61220EA6 FOREIGN KEY (creator_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE avatar ADD CONSTRAINT FK_1677722F9B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC9B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCF60E2305 FOREIGN KEY (deal_id) REFERENCES deals (id)');
        $this->addSql('ALTER TABLE deals ADD CONSTRAINT FK_EF39849B98DE13AC FOREIGN KEY (partenaire_id) REFERENCES partenaire (id)');
        $this->addSql('ALTER TABLE deals ADD CONSTRAINT FK_EF39849B61220EA6 FOREIGN KEY (creator_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE groupe_deals ADD CONSTRAINT FK_BA944ACB7A45358C FOREIGN KEY (groupe_id) REFERENCES groupe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE groupe_deals ADD CONSTRAINT FK_BA944ACB97ED4789 FOREIGN KEY (deals_id) REFERENCES deals (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE thumb ADD CONSTRAINT FK_1537D1DBBA9CD190 FOREIGN KEY (commentaire_id) REFERENCES commentaire (id)');
        $this->addSql('ALTER TABLE thumb ADD CONSTRAINT FK_1537D1DB9B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A1085649B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A10856497ED4789 FOREIGN KEY (deals_id) REFERENCES deals (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE account_badge DROP FOREIGN KEY FK_C87F916C9B6B5FBA');
        $this->addSql('ALTER TABLE account_deals DROP FOREIGN KEY FK_D9B65DEA9B6B5FBA');
        $this->addSql('ALTER TABLE alerte DROP FOREIGN KEY FK_3AE753A61220EA6');
        $this->addSql('ALTER TABLE avatar DROP FOREIGN KEY FK_1677722F9B6B5FBA');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC9B6B5FBA');
        $this->addSql('ALTER TABLE deals DROP FOREIGN KEY FK_EF39849B61220EA6');
        $this->addSql('ALTER TABLE thumb DROP FOREIGN KEY FK_1537D1DB9B6B5FBA');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A1085649B6B5FBA');
        $this->addSql('ALTER TABLE account_badge DROP FOREIGN KEY FK_C87F916CF7A2C2FC');
        $this->addSql('ALTER TABLE thumb DROP FOREIGN KEY FK_1537D1DBBA9CD190');
        $this->addSql('ALTER TABLE account_deals DROP FOREIGN KEY FK_D9B65DEA97ED4789');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCF60E2305');
        $this->addSql('ALTER TABLE groupe_deals DROP FOREIGN KEY FK_BA944ACB97ED4789');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A10856497ED4789');
        $this->addSql('ALTER TABLE groupe_deals DROP FOREIGN KEY FK_BA944ACB7A45358C');
        $this->addSql('ALTER TABLE deals DROP FOREIGN KEY FK_EF39849B98DE13AC');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE account_badge');
        $this->addSql('DROP TABLE account_deals');
        $this->addSql('DROP TABLE alerte');
        $this->addSql('DROP TABLE avatar');
        $this->addSql('DROP TABLE badge');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE deals');
        $this->addSql('DROP TABLE groupe');
        $this->addSql('DROP TABLE groupe_deals');
        $this->addSql('DROP TABLE partenaire');
        $this->addSql('DROP TABLE thumb');
        $this->addSql('DROP TABLE vote');
    }
}
