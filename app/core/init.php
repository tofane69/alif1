<?php
// Start session
session_name(defined('SESSION_NAME') ? SESSION_NAME : 'kb_session');
session_start();

// Load Config
require_once dirname(__DIR__) . '/../config/config.php';

// Autoload Core Libraries
spl_autoload_register(function($className){
    $file = dirname(__FILE__) . '/' . $className . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Load Helper Functions
require_once 'helpers.php';
require_once 'security_helpers.php';

// Set default timezone
date_default_timezone_set('Asia/Tehran');

// Error Reporting (Disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>