<?php
// Header/Navigation component
// This is included at the top of every page
$current_page = basename($_SERVER['PHP_SELF'] ?? 'index.php');
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/messages.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title ?? 'OarNote'); ?></title>
    <meta name="theme-color" content="#050916">
    <link rel="stylesheet" href="/css/style.css?v=<?php echo filemtime(__DIR__ . '/../css/style.css'); ?>">
</head>
<body>
<nav class="navbar">
    <div class="navbar-container">
        <div class="navbar-brand">
            <a href="/" class="logo">
                <span class="logo-icon" aria-hidden="true"><svg viewBox="0 0 32 32"><path d="M7 24 23 8m-1-3 5 5-4 4-5-5 4-4ZM10 21l3 3-5 4H4v-4l6-3Z"/></svg></span>
                <span class="logo-text">OarNote</span>
            </a>
        </div>
        <div class="navbar-menu" id="navbarMenu">
            <ul class="navbar-list">
                <li><a href="/" class="nav-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>">Home</a></li>
                <?php if (is_logged_in()): ?>
                <li><a href="/pages/dashboard.php" class="nav-link <?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>">Sessions</a></li>
                <?php endif; ?>
                <?php if (is_logged_in()): ?>
                <li><span class="nav-user"><?php echo htmlspecialchars($_SESSION['user']['first_name'] ?? 'Account'); ?></span></li>
                <li><form action="/auth/logout.php" method="post" class="nav-form"><input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>"><button class="nav-link nav-login" type="submit">Log out</button></form></li>
                <?php else: ?>
                <li><a href="/auth/login.php" class="nav-link <?php echo $current_page === 'login.php' ? 'active' : ''; ?>">Log in</a></li>
                <li><a href="/auth/register.php" class="nav-link nav-login">Create account</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <button class="navbar-toggle" id="navbarToggle" type="button" aria-label="Toggle navigation" aria-expanded="false">
            <span class="hamburger"></span>
        </button>
    </div>
</nav>

<main class="container">

<?php $flash_message = get_session_message(); if ($flash_message): ?>
    <?php display_message($flash_message['type'] ?? 'info', $flash_message['text'] ?? ''); ?>
<?php endif; ?>
