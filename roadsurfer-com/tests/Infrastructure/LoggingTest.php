<?php

namespace App\Tests\Infrastructure;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoggingTest extends KernelTestCase
{
    private ContainerInterface $container;
    private string $logFile;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->container = static::getContainer();

        $logDir = $this->container->getParameter('kernel.logs_dir');
        $environment = $this->container->getParameter('kernel.environment');
        $this->logFile = $logDir . '/' . $environment . '.log';

        // Ensure log directory exists
        if (! is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
    }

    public function testLoggerServiceIsAvailable(): void
    {
        $logger = $this->container->get('logger');

        $this->assertNotNull($logger);
        $this->assertInstanceOf(\Psr\Log\LoggerInterface::class, $logger);
    }

    public function testLoggerCanWriteMessages(): void
    {
        $logger = $this->container->get('logger');

        // Clear log file before test
        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }

        // Write test messages
        $testMessage = 'Test log message ' . uniqid();
        $logger->info($testMessage);

        // Wait a moment for log to be written
        sleep(1);

        // Verify log file exists and contains our message
        $this->assertFileExists($this->logFile);

        $logContent = file_get_contents($this->logFile);
        $this->assertStringContainsString($testMessage, $logContent);
    }

    public function testLogFilesAreWritable(): void
    {
        $logDir = $this->container->getParameter('kernel.logs_dir');

        $this->assertDirectoryExists($logDir);
        $this->assertDirectoryIsWritable($logDir);
    }

    public function testDifferentLogLevels(): void
    {
        $logger = $this->container->get('logger');

        // Clear log file
        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }

        // Test different log levels
        $debugMessage = 'Debug message ' . uniqid();
        $infoMessage = 'Info message ' . uniqid();
        $errorMessage = 'Error message ' . uniqid();

        $logger->debug($debugMessage);
        $logger->info($infoMessage);
        $logger->error($errorMessage);

        // Wait for logs to be written
        sleep(1);

        // Check if log file exists before reading
        if (file_exists($this->logFile)) {
            $logContent = file_get_contents($this->logFile);

            // Verify all messages are logged
            $this->assertStringContainsString($debugMessage, $logContent);
            $this->assertStringContainsString($infoMessage, $logContent);
            $this->assertStringContainsString($errorMessage, $logContent);
        } else {
            $this->fail('Log file was not created');
        }
    }

    public function testLogFormatIsCorrect(): void
    {
        $logger = $this->container->get('logger');

        // Clear log file
        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }

        $testMessage = 'Format test ' . uniqid();
        $logger->info($testMessage);

        // Wait for log to be written
        sleep(1);

        // Check if log file exists before reading
        if (file_exists($this->logFile)) {
            $logContent = file_get_contents($this->logFile);
            $logLines = explode("\n", trim($logContent));
            $lastLine = end($logLines);

            // Verify log format contains timestamp and level
            $this->assertStringContainsString($testMessage, $lastLine);
            $this->assertStringContainsString('app.INFO', $lastLine); // Monolog format
            $this->assertMatchesRegularExpression('/\[\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $lastLine); // ISO date format anywhere in line
        } else {
            $this->fail('Log file was not created');
        }
    }
}
