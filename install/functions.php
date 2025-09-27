<?php
/**
 * Checks server requirements for the application.
 *
 * @return array An array of requirement checks with their status and message.
 */
function check_requirements() {
    $requirements = [];

    // 1. Check PHP Version
    $requirements[] = [
        'name' => 'نسخه PHP >= 8.0',
        'status' => version_compare(PHP_VERSION, '8.0.0', '>='),
        'message' => 'نسخه فعلی PHP: ' . PHP_VERSION,
    ];

    // 2. Check if config directory is writable
    $config_dir = PROJECT_ROOT . '/config';
    $requirements[] = [
        'name' => 'پوشه /config قابل نوشتن باشد',
        'status' => is_writable($config_dir),
        'message' => 'مسیر: ' . $config_dir,
    ];

    // 3. Check if uploads directory is writable
    $uploads_dir = PROJECT_ROOT . '/uploads';
    $requirements[] = [
        'name' => 'پوشه /uploads قابل نوشتن باشد',
        'status' => is_writable($uploads_dir),
        'message' => 'مسیر: ' . $uploads_dir,
    ];

    // 4. Check if cache directory is writable
    $cache_dir = PROJECT_ROOT . '/cache';
    $requirements[] = [
        'name' => 'پوشه /cache قابل نوشتن باشد',
        'status' => is_writable($cache_dir),
        'message' => 'مسیر: ' . $cache_dir,
    ];

    return $requirements;
}

/**
 * Check if all requirements are met.
 *
 * @param array $requirements The array of requirements from check_requirements().
 * @return bool True if all requirements are met, false otherwise.
 */
function all_requirements_met($requirements) {
    foreach ($requirements as $requirement) {
        if (!$requirement['status']) {
            return false;
        }
    }
    return true;
}

/**
 * Imports an SQL file into the database.
 *
 * @param mysqli $conn The database connection object.
 * @param string $sqlFilePath The path to the SQL file.
 * @return bool True on success, false on failure.
 */
function import_sql_file($conn, $sqlFilePath) {
    if (!file_exists($sqlFilePath)) {
        return false;
    }

    $sql = file_get_contents($sqlFilePath);
    if ($sql === false) {
        return false;
    }

    // Split the SQL file into individual queries.
    // This is a more robust way than multi_query on some systems.
    $queries = preg_split('/;\s*(\r\n|\n|\r)/', $sql);

    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            if ($conn->query($query) === FALSE) {
                // If a query fails, stop and return false.
                // The error can be retrieved with $conn->error
                return false;
            }
        }
    }

    return true;
}

/**
 * Creates the final config.php file from a template.
 *
 * @param array $db_details Database connection details.
 * @param array $site_config Site configuration details.
 * @return bool True on success, false on failure.
 */
function create_config_file($db_details, $site_config) {
    $config_template = file_get_contents(INSTALL_ROOT . '/config.template.php');
    if ($config_template === false) {
        return false;
    }

    // Replace placeholders with actual values
    $config_content = str_replace(
        [
            '{{DB_HOST}}',
            '{{DB_NAME}}',
            '{{DB_USER}}',
            '{{DB_PASS}}',
            '{{URL_ROOT}}',
            '{{SITE_NAME}}',
            '{{CSRF_SECRET}}'
        ],
        [
            $db_details['host'],
            $db_details['name'],
            $db_details['user'],
            $db_details['pass'],
            $site_config['url_root'],
            $site_config['site_name'],
            bin2hex(random_bytes(32)) // Generate a new random secret
        ],
        $config_template
    );

    $config_path = PROJECT_ROOT . '/config/config.php';
    return file_put_contents($config_path, $config_content) !== false;
}
?>