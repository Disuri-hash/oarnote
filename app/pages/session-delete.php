<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/messages.php';
require_role('coach');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? null)) {
    http_response_code(405);
    exit('Invalid deletion request.');
}
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) { http_response_code(400); exit('Invalid session.'); }
require '../config/database.php';
$statement = $pdo->prepare('DELETE FROM sessions WHERE id = ? AND coach_id = ?');
$statement->execute([$id, (int) $_SESSION['user_id']]);
set_session_message($statement->rowCount() ? 'success' : 'error', $statement->rowCount() ? 'Training session deleted.' : 'Session not found or not owned by you.');
header('Location: /pages/dashboard.php');
exit;
