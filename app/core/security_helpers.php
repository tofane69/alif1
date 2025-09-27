<?php

// --- CSRF Protection ---

/**
 * Generate and store a CSRF token if one doesn't exist.
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

/**
 * Generate a hidden input field with the CSRF token.
 *
 * @return string The HTML input field.
 */
function csrf_input() {
    generate_csrf_token(); // Ensure token exists
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($_SESSION['csrf_token']) . '">';
}

/**
 * Check if the submitted CSRF token is valid.
 *
 * @return bool True if valid, false otherwise.
 */
function check_csrf_token() {
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
        return false;
    }
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        return false;
    }
    // Token is valid, clear it to prevent reuse (optional but good practice)
    // unset($_SESSION['csrf_token']);
    return true;
}

/**
 * Generate a meta tag with the CSRF token for AJAX requests.
 *
 * @return string The HTML meta tag.
 */
function csrf_meta() {
    generate_csrf_token();
    return '<meta name="csrf-token" content="' . htmlspecialchars($_SESSION['csrf_token']) . '">';
}
?>