<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150325141644 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE job (id INT AUTO_INCREMENT NOT NULL, job VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person_movie_ref (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, job_id INT DEFAULT NULL, movie_id INT DEFAULT NULL, role VARCHAR(255) NOT NULL, INDEX IDX_7316981B217BBB47 (person_id), INDEX IDX_7316981BBE04EA9 (job_id), INDEX IDX_7316981B8F93B6FC (movie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person (id INT AUTO_INCREMENT NOT NULL, imdbId INT NOT NULL, name VARCHAR(255) NOT NULL, birthAt DATETIME NOT NULL, deathAt DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person_job_ref (person_id INT NOT NULL, job_id INT NOT NULL, INDEX IDX_F2241EBB217BBB47 (person_id), INDEX IDX_F2241EBBBE04EA9 (job_id), PRIMARY KEY(person_id, job_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE person_movie_ref ADD CONSTRAINT FK_7316981B217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE person_movie_ref ADD CONSTRAINT FK_7316981BBE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
        $this->addSql('ALTER TABLE person_movie_ref ADD CONSTRAINT FK_7316981B8F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id)');
        $this->addSql('ALTER TABLE person_job_ref ADD CONSTRAINT FK_F2241EBB217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE person_job_ref ADD CONSTRAINT FK_F2241EBBBE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE person_movie_ref DROP FOREIGN KEY FK_7316981BBE04EA9');
        $this->addSql('ALTER TABLE person_job_ref DROP FOREIGN KEY FK_F2241EBBBE04EA9');
        $this->addSql('ALTER TABLE person_movie_ref DROP FOREIGN KEY FK_7316981B217BBB47');
        $this->addSql('ALTER TABLE person_job_ref DROP FOREIGN KEY FK_F2241EBB217BBB47');
        $this->addSql('DROP TABLE job');
        $this->addSql('DROP TABLE person_movie_ref');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE person_job_ref');
    }
}
