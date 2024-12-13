<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241213082512 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE answer (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, id_user_id INTEGER NOT NULL, id_quiz_id INTEGER NOT NULL, score SMALLINT NOT NULL, time TIME NOT NULL, date_time DATETIME NOT NULL, CONSTRAINT FK_DADD4A2579F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_DADD4A255BA17805 FOREIGN KEY (id_quiz_id) REFERENCES quiz (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_DADD4A2579F37AE5 ON answer (id_user_id)');
        $this->addSql('CREATE INDEX IDX_DADD4A255BA17805 ON answer (id_quiz_id)');
        $this->addSql('CREATE TABLE possible_answer (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, id_question_id INTEGER NOT NULL, is_true BOOLEAN NOT NULL, value VARCHAR(255) NOT NULL, CONSTRAINT FK_3D79739D6353B48 FOREIGN KEY (id_question_id) REFERENCES question (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_3D79739D6353B48 ON possible_answer (id_question_id)');
        $this->addSql('CREATE TABLE question (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, id_type_id INTEGER NOT NULL, id_quiz_id INTEGER NOT NULL, points SMALLINT NOT NULL, statement VARCHAR(510) NOT NULL, CONSTRAINT FK_B6F7494E1BD125E3 FOREIGN KEY (id_type_id) REFERENCES question_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_B6F7494E5BA17805 FOREIGN KEY (id_quiz_id) REFERENCES quiz (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_B6F7494E1BD125E3 ON question (id_type_id)');
        $this->addSql('CREATE INDEX IDX_B6F7494E5BA17805 ON question (id_quiz_id)');
        $this->addSql('CREATE TABLE question_type (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE quiz (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, id_user_id INTEGER NOT NULL, id_theme_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, max_time TIME DEFAULT NULL, difficulty VARCHAR(255) NOT NULL, CONSTRAINT FK_A412FA9279F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_A412FA929D99812 FOREIGN KEY (id_theme_id) REFERENCES theme (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_A412FA9279F37AE5 ON quiz (id_user_id)');
        $this->addSql('CREATE INDEX IDX_A412FA929D99812 ON quiz (id_theme_id)');
        $this->addSql('CREATE TABLE theme (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE "user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME ON "user" (username)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE answer');
        $this->addSql('DROP TABLE possible_answer');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE question_type');
        $this->addSql('DROP TABLE quiz');
        $this->addSql('DROP TABLE theme');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
