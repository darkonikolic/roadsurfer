<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250110000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial database setup';
    }

    /**
     * @SuppressWarnings(PHPMD.ShortMethodName)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function up(Schema $schema): void
    {
        // This migration is intentionally empty
        // It serves as a baseline for future migrations
        // Add your actual database schema here when needed
        $this->addSql('SELECT 1');
    }

    /**
     * @SuppressWarnings(PHPMD.ShortMethodName)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function down(Schema $schema): void
    {
        // This migration is intentionally empty
        // It serves as a baseline for future migrations
        $this->addSql('SELECT 1');
    }
}
