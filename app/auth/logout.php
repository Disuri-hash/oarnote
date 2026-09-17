<?php
session_start();
require_once '../includes/auth.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? null)) {
    http_response_code(405);
    exit('Invalid logout request.');
}
logout_user();
session_start();
$_SESSION['message'] = ['type' => 'success', 'text' => 'You have been logged out safely.'];
header('Location: /');
exit;
