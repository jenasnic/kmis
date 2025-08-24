<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Exception\IrreversibleMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250825000000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adherent (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(55) NOT NULL, last_name VARCHAR(55) NOT NULL, gender VARCHAR(55) NOT NULL, birth_date DATETIME NOT NULL, phone VARCHAR(55) DEFAULT NULL, email VARCHAR(255) NOT NULL, pseudonym VARCHAR(255) DEFAULT NULL, re_enrollment_to_notify TINYINT(1) NOT NULL, picture_url VARCHAR(255) DEFAULT NULL, address_street VARCHAR(255) DEFAULT NULL, address_zip_code VARCHAR(25) DEFAULT NULL, address_city VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE configuration (code VARCHAR(55) NOT NULL, value LONGTEXT NOT NULL, PRIMARY KEY(code)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE emergency (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(55) NOT NULL, last_name VARCHAR(55) NOT NULL, phone VARCHAR(55) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE legal_representative (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(55) NOT NULL, last_name VARCHAR(55) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE news (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT DEFAULT NULL, details LONGTEXT DEFAULT NULL, active TINYINT(1) NOT NULL, rank INT NOT NULL, picture_url VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, adherent_id INT NOT NULL, season_id INT NOT NULL, date DATETIME NOT NULL, amount DOUBLE PRECISION NOT NULL, comment LONGTEXT DEFAULT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_6D28840D25F06C53 (adherent_id), INDEX IDX_6D28840D4EC001D1 (season_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_ancv (id INT NOT NULL, number VARCHAR(55) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_cash (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_check (id INT NOT NULL, number VARCHAR(55) NOT NULL, cashing_date DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_discount (id INT NOT NULL, discount VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_hello_asso (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_pass (id INT NOT NULL, number VARCHAR(55) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_transfer (id INT NOT NULL, label VARCHAR(55) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE price_option (id INT AUTO_INCREMENT NOT NULL, season_id INT NOT NULL, label VARCHAR(255) NOT NULL, amount DOUBLE PRECISION NOT NULL, rank INT NOT NULL, INDEX IDX_171FA8E04EC001D1 (season_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE purpose (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, rank INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE re_enrollment_token (id VARCHAR(55) NOT NULL, adherent_id INT NOT NULL, season_id INT NOT NULL, expires_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_6BF1B92F25F06C53 (adherent_id), INDEX IDX_6BF1B92F4EC001D1 (season_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE registration (id INT AUTO_INCREMENT NOT NULL, legal_representative_id INT DEFAULT NULL, purpose_id INT DEFAULT NULL, price_option_id INT DEFAULT NULL, emergency_id INT DEFAULT NULL, adherent_id INT NOT NULL, season_id INT NOT NULL, comment LONGTEXT DEFAULT NULL, private_note LONGTEXT DEFAULT NULL, licence_number TINYTEXT DEFAULT NULL, licence_date DATETIME DEFAULT NULL, medical_certificate_url VARCHAR(255) DEFAULT NULL, licence_form_url VARCHAR(255) DEFAULT NULL, use_pass_citizen TINYINT(1) NOT NULL, pass_citizen_url VARCHAR(255) DEFAULT NULL, use_pass_sport TINYINT(1) NOT NULL, pass_sport_url VARCHAR(255) DEFAULT NULL, use_ccas TINYINT(1) NOT NULL, registered_at DATETIME NOT NULL, copyright_authorization TINYINT(1) NOT NULL, with_legal_representative TINYINT(1) NOT NULL, registration_type VARCHAR(55) NOT NULL, re_enrollment TINYINT(1) NOT NULL, verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_62A8A7A7E05BB347 (legal_representative_id), INDEX IDX_62A8A7A77FC21131 (purpose_id), INDEX IDX_62A8A7A724752E93 (price_option_id), UNIQUE INDEX UNIQ_62A8A7A7D904784C (emergency_id), UNIQUE INDEX UNIQ_62A8A7A725F06C53 (adherent_id), INDEX IDX_62A8A7A74EC001D1 (season_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE season (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(55) NOT NULL, active TINYINT(1) NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, role VARCHAR(55) NOT NULL, enabled TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D25F06C53 FOREIGN KEY (adherent_id) REFERENCES adherent (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D4EC001D1 FOREIGN KEY (season_id) REFERENCES season (id)');
        $this->addSql('ALTER TABLE payment_ancv ADD CONSTRAINT FK_36A2D249BF396750 FOREIGN KEY (id) REFERENCES payment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE payment_cash ADD CONSTRAINT FK_273A72CDBF396750 FOREIGN KEY (id) REFERENCES payment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE payment_check ADD CONSTRAINT FK_6BC79AA1BF396750 FOREIGN KEY (id) REFERENCES payment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE payment_discount ADD CONSTRAINT FK_A126F666BF396750 FOREIGN KEY (id) REFERENCES payment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE payment_hello_asso ADD CONSTRAINT FK_86390FAFBF396750 FOREIGN KEY (id) REFERENCES payment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE payment_pass ADD CONSTRAINT FK_EFF34350BF396750 FOREIGN KEY (id) REFERENCES payment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE payment_transfer ADD CONSTRAINT FK_F2E1A8BF396750 FOREIGN KEY (id) REFERENCES payment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE price_option ADD CONSTRAINT FK_171FA8E04EC001D1 FOREIGN KEY (season_id) REFERENCES season (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE re_enrollment_token ADD CONSTRAINT FK_6BF1B92F25F06C53 FOREIGN KEY (adherent_id) REFERENCES adherent (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE re_enrollment_token ADD CONSTRAINT FK_6BF1B92F4EC001D1 FOREIGN KEY (season_id) REFERENCES season (id)');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A7E05BB347 FOREIGN KEY (legal_representative_id) REFERENCES legal_representative (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A77FC21131 FOREIGN KEY (purpose_id) REFERENCES purpose (id)');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A724752E93 FOREIGN KEY (price_option_id) REFERENCES price_option (id)');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A7D904784C FOREIGN KEY (emergency_id) REFERENCES emergency (id)');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A725F06C53 FOREIGN KEY (adherent_id) REFERENCES adherent (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A74EC001D1 FOREIGN KEY (season_id) REFERENCES season (id)');
    }

    public function down(Schema $schema): void
    {
        throw new IrreversibleMigration();
    }
}
