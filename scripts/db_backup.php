<?php
// A simple database backup script
// To be run from CLI: php scripts/db_backup.php

// Load the main config file to get DB credentials
require_once dirname(__DIR__) . '/config/config.php';

// --- Configuration ---
$backupDir = dirname(__DIR__) . '/backups';
$retentionDays = 7; // How many days of backups to keep

// --- Script Logic ---

// 1. Set backup file name with timestamp
$backupFile = $backupDir . '/db_backup_' . date('Y-m-d_H-i-s') . '.sql.gz';

// 2. Build the mysqldump command
// Using --no-tablespaces is often required on shared hosting
$command = sprintf(
    'mysqldump --user=%s --password=%s --host=%s --no-tablespaces %s | gzip > %s',
    escapeshellarg(DB_USER),
    escapeshellarg(DB_PASS),
    escapeshellarg(DB_HOST),
    escapeshellarg(DB_NAME),
    escapeshellarg($backupFile)
);

// 3. Execute the command
echo "Creating backup for database: " . DB_NAME . "...\n";
$output = null;
$resultCode = null;
exec($command, $output, $resultCode);

// 4. Check the result
if ($resultCode === 0) {
    echo "Backup created successfully: " . $backupFile . "\n";
} else {
    echo "ERROR: Backup failed. Result code: " . $resultCode . "\n";
    // You might want to log the error or send an email notification here
    exit(1);
}

// 5. Clean up old backups
echo "Cleaning up old backups (older than $retentionDays days)...\n";
$files = glob($backupDir . '/db_backup_*.sql.gz');
$now = time();

foreach ($files as $file) {
    if (is_file($file)) {
        // Check if the file is older than the retention period
        if ($now - filemtime($file) >= ($retentionDays * 24 * 60 * 60)) {
            echo "  - Deleting old backup: " . basename($file) . "\n";
            unlink($file);
        }
    }
}

echo "Backup process complete.\n";
exit(0);
?>