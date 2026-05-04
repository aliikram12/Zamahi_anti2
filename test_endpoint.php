<?php
/**
 * Test endpoint to check PHP and database connectivity
 */

header('Content-Type: application/json');

// Start session for testing
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate a test CSRF token
if (empty($_SESSION['test_csrf'])) {
    $_SESSION['test_csrf'] = bin2hex(random_bytes(32));
}

echo json_encode([
    'success' => true,
    'message' => 'PHP is working!',
    'post_data' => $_POST,
    'session_id' => session_id(),
    'session_status' => session_status(),
    'csrf_token' => $_SESSION['test_csrf'] ?? 'No token',
    'session_keys' => array_keys($_SESSION)
]);
