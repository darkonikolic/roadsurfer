<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
    require dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

// Force test environment
$_SERVER['APP_ENV'] = 'test';
$_ENV['APP_ENV'] = 'test';
putenv('APP_ENV=test');

// Ensure log directory exists for tests
$logDir = dirname(__DIR__) . '/var/log';
if (! is_dir($logDir)) {
    mkdir($logDir, 0777, true);
}

// Ensure var directory is writable
$varDir = dirname(__DIR__) . '/var';
if (! is_dir($varDir)) {
    mkdir($varDir, 0777, true);
}

// Set proper permissions for test environment
chmod($logDir, 0777);
if (is_dir($varDir)) {
    chmod($varDir, 0777);
}
