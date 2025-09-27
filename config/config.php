<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default for XAMPP
define('DB_NAME', 'kb');

// App Root
define('APP_ROOT', dirname(dirname(__FILE__))); // /path/to/your/project

// URL Root (e.g., http://localhost/kb_project)
// TODO: This should be set dynamically or configured for the specific environment
define('URL_ROOT', 'http://localhost/kb_project');

// Site Name
define('SITE_NAME', 'مرکز دانش');

// Default password for new users or password resets
define('DEFAULT_PASSWORD', 'Abcd@1234');

// Session config
define('SESSION_NAME', 'kb_session');

// File upload settings
define('UPLOAD_PATH', APP_ROOT . '/uploads');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 MB
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'mp3', 'wav']);

// Security
define('CSRF_TOKEN_SECRET', 'a_very_secret_and_long_random_string_for_csrf'); // Change this to a random string

// Login attempt limits
define('LOGIN_ATTEMPT_LIMIT', 5);
define('LOGIN_LOCKOUT_TIME', 15 * 60); // 15 minutes in seconds

// User Roles (Must match the IDs in the `roles` table)
define('ROLE_ADMIN', 1);
define('ROLE_QUALITY_ASSURANCE', 2);
define('ROLE_SUPERVISOR', 3);
define('ROLE_EMPLOYEE', 4);
?>