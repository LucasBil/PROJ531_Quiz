<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241206084708 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE answer (id INT AUTO_INCREMENT NOT NULL, id_user_id INT NOT NULL, id_quiz_id INT NOT NULL, score SMALLINT NOT NULL, time TIME NOT NULL, date_time DATETIME NOT NULL, INDEX IDX_DADD4A2579F37AE5 (id_user_id), INDEX IDX_DADD4A255BA17805 (id_quiz_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE possible_answer (id INT AUTO_INCREMENT NOT NULL, id_question_id INT NOT NULL, is_true TINYINT(1) NOT NULL, value VARCHAR(255) NOT NULL, INDEX IDX_3D79739D6353B48 (id_question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, id_type_id INT NOT NULL, id_quiz_id INT NOT NULL, points SMALLINT NOT NULL, statement VARCHAR(510) NOT NULL, INDEX IDX_B6F7494E1BD125E3 (id_type_id), INDEX IDX_B6F7494E5BA17805 (id_quiz_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quiz (id INT AUTO_INCREMENT NOT NULL, id_user_id INT NOT NULL, id_theme_id INT NOT NULL, name VARCHAR(255) NOT NULL, max_time TIME DEFAULT NULL, INDEX IDX_A412FA9279F37AE5 (id_user_id), INDEX IDX_A412FA929D99812 (id_theme_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE theme (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A2579F37AE5 FOREIGN KEY (id_user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A255BA17805 FOREIGN KEY (id_quiz_id) REFERENCES quiz (id)');
        $this->addSql('ALTER TABLE possible_answer ADD CONSTRAINT FK_3D79739D6353B48 FOREIGN KEY (id_question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E1BD125E3 FOREIGN KEY (id_type_id) REFERENCES question_type (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E5BA17805 FOREIGN KEY (id_quiz_id) REFERENCES quiz (id)');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA9279F37AE5 FOREIGN KEY (id_user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA929D99812 FOREIGN KEY (id_theme_id) REFERENCES theme (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A2579F37AE5');
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A255BA17805');
        $this->addSql('ALTER TABLE possible_answer DROP FOREIGN KEY FK_3D79739D6353B48');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E1BD125E3');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E5BA17805');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA9279F37AE5');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA929D99812');
        $this->addSql('DROP TABLE answer');
        $this->addSql('DROP TABLE possible_answer');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE question_type');
        $this->addSql('DROP TABLE quiz');
        $this->addSql('DROP TABLE theme');
    }
}
