<?php

function display_message($type, $message) {
    if (!$message) return;
    $allowed = ['success', 'error', 'warning', 'info'];
    $type = in_array($type, $allowed, true) ? $type : 'info';
    $icons = ['success' => '&#10003;', 'error' => '&#10005;', 'warning' => '!', 'info' => 'i'];
    echo '<div class="alert alert-' . $type . '" role="alert">';
    echo '<span class="alert-icon">' . $icons[$type] . '</span>';
    echo '<span class="alert-text">' . htmlspecialchars($message) . '</span></div>';
}

function get_session_message() {
    if (empty($_SESSION['message'])) return null;
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
    return $message;
}

function set_session_message($type, $message) {
    $_SESSION['message'] = ['type' => $type, 'text' => $message];
}
