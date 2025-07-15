<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractIntegrationTestCase extends KernelTestCase
{
    protected ContainerInterface $container;
    protected EntityManagerInterface $entityManager;
    protected Connection $connection;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->container     = static::getContainer();
        $this->entityManager = $this->container->get('doctrine.orm.entity_manager');
        $this->connection    = $this->container->get('doctrine.dbal.default_connection');

        // Start transaction for test isolation
        $this->beginTransaction();

    }

    protected function tearDown(): void
    {
        // Rollback transaction to clean up database changes
        $this->rollbackTransaction();

        parent::tearDown();
    }

    /**
     * Begin database transaction for test isolation
     */
    protected function beginTransaction(): void
    {
        if (!$this->connection->isTransactionActive()) {
            $this->connection->beginTransaction();
        }
    }

    /**
     * Rollback database transaction to clean up changes
     */
    protected function rollbackTransaction(): void
    {
        if ($this->connection->isTransactionActive()) {
            $this->connection->rollBack();
        }
    }

    /**
     * Commit database transaction (use carefully in tests)
     */
    protected function commitTransaction(): void
    {
        if ($this->connection->isTransactionActive()) {
            $this->connection->commit();
        }
    }

    /**
     * Assert that database table is empty
     */
    protected function assertTableIsEmpty(string $tableName): void
    {
        $count = $this->connection->executeQuery("SELECT COUNT(*) FROM {$tableName}")->fetchOne();
        $this->assertEquals(0, $count, "Table '{$tableName}' should be empty");
    }

    /**
     * Assert that database table has specific number of records
     */
    protected function assertTableHasRecordCount(string $tableName, int $expectedCount): void
    {
        $count = $this->connection->executeQuery("SELECT COUNT(*) FROM {$tableName}")->fetchOne();
        $this->assertEquals($expectedCount, $count, "Table '{$tableName}' should have {$expectedCount} records");
    }

    /**
     * Clean up all test data
     */
    protected function cleanupTestData(): void
    {
        // Clear database tables
        $this->connection->executeStatement('DELETE FROM fruits');
        $this->connection->executeStatement('DELETE FROM vegetables');

    }
}
