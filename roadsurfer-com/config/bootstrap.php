<?php

// Suppress deprecation warnings for PHP 8.3+
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Set timezone
date_default_timezone_set('UTC');

// Set error handler to filter deprecation warnings
set_error_handler(function($severity, $message, $file, $line) {
    if ($severity === E_DEPRECATED || $severity === E_USER_DEPRECATED) {
        return true; // Suppress deprecation warnings
    }
    return false; // Let other errors be handled normally
}); 