<?php
/**
 * Path Helper - Add this at the top of each PHP file
 * This ensures all paths work correctly with clean URLs
 */

// Get the base URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$base_url = $protocol . "://" . $_SERVER['HTTP_HOST'];

// Define base path constant
define('BASE_URL', $base_url);
define('BASE_PATH', '/');

/**
 * Helper function to generate absolute URLs
 */
function asset($path) {
    return BASE_URL . '/' . ltrim($path, '/');
}
?>
