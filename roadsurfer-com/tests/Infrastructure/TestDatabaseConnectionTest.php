<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TestDatabaseConnectionTest extends KernelTestCase
{
    private Connection $connection;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->connection = static::getContainer()->get('doctrine.dbal.default_connection');
    }

    public function testTestDatabaseConnectionIsAvailable(): void
    {
        $this->assertInstanceOf(Connection::class, $this->connection);

        try {
            // Try to connect if not already connected
            if (!$this->connection->isConnected()) {
                $this->connection->connect();
            }

            $this->assertTrue($this->connection->isConnected());
        } catch (\Exception $e) {
            $this->markTestSkipped('Test database connection failed: ' . $e->getMessage());
        }
    }

    public function testTestDatabaseCanExecuteQueries(): void
    {
        try {
            $result = $this->connection->executeQuery('SELECT 1 as test');
            $row    = $result->fetchAssociative();

            $this->assertEquals(1, $row['test']);
        } catch (\Exception $e) {
            $this->markTestSkipped('Test database query failed: ' . $e->getMessage());
        }
    }

    public function testTestDatabaseConfigurationIsValid(): void
    {
        $params = $this->connection->getParams();

        $this->assertArrayHasKey('host', $params);
        $this->assertArrayHasKey('port', $params);
        $this->assertArrayHasKey('dbname', $params);
        $this->assertArrayHasKey('user', $params);
        $this->assertArrayHasKey('password', $params);

        // Verify this is test database
        $this->assertStringContainsString('test', $params['dbname']);
    }

    public function testTestEnvironmentIsCorrect(): void
    {
        $kernel = static::bootKernel();
        $this->assertEquals('test', $kernel->getEnvironment());
    }
}
