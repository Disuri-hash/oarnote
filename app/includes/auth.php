<?php
// Authentication and authorization helpers.

function is_logged_in() {
    return !empty($_SESSION['user_id']);
}

function get_authenticated_user() {
    return $_SESSION['user'] ?? null;
}

function get_user_role() {
    return $_SESSION['user_role'] ?? ($_SESSION['user']['role'] ?? null);
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return is_string($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function redirect_if_logged_in() {
    if (is_logged_in()) {
        header('Location: /pages/dashboard.php');
        exit;
    }
}

function login_user(array $user) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user'] = ['id' => (int) $user['id'], 'email' => $user['email'], 'first_name' => $user['first_name'], 'last_name' => $user['last_name'], 'role' => $user['role']];
    unset($_SESSION['csrf_token']);
}

function logout_user() {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function require_login() {
    if (!is_logged_in()) {
        $_SESSION['message'] = ['type' => 'info', 'text' => 'Please log in to view training sessions.'];
        header('Location: /');
        exit;
    }
}

function require_role($role) {
    require_login();
    if (get_user_role() !== $role) {
        http_response_code(403);
        exit('You do not have permission to view this page.');
    }
}
