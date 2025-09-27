<?php
// Database Configuration
define('DB_HOST', '{{DB_HOST}}');
define('DB_USER', '{{DB_USER}}');
define('DB_PASS', '{{DB_PASS}}');
define('DB_NAME', '{{DB_NAME}}');

// App Root - This is calculated automatically
define('APP_ROOT', dirname(dirname(__FILE__)));

// URL Root
define('URL_ROOT', '{{URL_ROOT}}');

// Site Name
define('SITE_NAME', '{{SITE_NAME}}');

// Default password for new users or password resets
define('DEFAULT_PASSWORD', 'Abcd@1234');

// Session config
define('SESSION_NAME', 'kb_session');

// File upload settings
define('UPLOAD_PATH', APP_ROOT . '/uploads');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 MB
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'mp3', 'wav']);

// Security - CSRF Secret is generated during installation
define('CSRF_TOKEN_SECRET', '{{CSRF_SECRET}}');

// Login attempt limits
define('LOGIN_ATTEMPT_LIMIT', 5);
define('LOGIN_LOCKOUT_TIME', 15 * 60); // 15 minutes in seconds

// User Roles (Must match the IDs in the `roles` table)
define('ROLE_ADMIN', 1);
define('ROLE_QUALITY_ASSURANCE', 2);
define('ROLE_SUPERVISOR', 3);
define('ROLE_EMPLOYEE', 4);
?>