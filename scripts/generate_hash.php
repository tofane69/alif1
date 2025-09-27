<?php
// Script to generate a secure password hash
// Usage: php generate_hash.php your_password_here

// Default password from the project requirements
$password = '12345';

// Use the password from command line argument if provided
if (isset($argv[1])) {
    $password = $argv[1];
}

// Generate the hash using bcrypt algorithm
$hash = password_hash($password, PASSWORD_BCRYPT);

echo "Password: " . $password . "\n";
echo "Hashed: " . $hash . "\n";
?>