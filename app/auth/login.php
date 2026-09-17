<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/messages.php';
redirect_if_logged_in();

$email = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $error = 'Your form expired. Please try again.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Enter a valid email address and password.';
    } else {
        require '../config/database.php';
        $statement = $pdo->prepare('SELECT id, email, password_hash, first_name, last_name, role FROM users WHERE email = ? LIMIT 1');
        $statement->execute([$email]);
        $user = $statement->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            login_user($user);
            set_session_message('success', 'Welcome back, ' . ($user['first_name'] ?: 'rower') . '.');
            header('Location: /pages/dashboard.php');
            exit;
        }
        $error = 'The email or password is incorrect.';
    }
}
$page_title = 'Log in - OarNote';
require '../includes/header.php';
?>
<section class="auth-layout">
 <div class="auth-intro"><p class="eyebrow">Welcome back</p><h1>Get your crew moving.</h1><p>Open your private training dashboard, review sessions, and keep building speed together.</p><div class="auth-oar" aria-hidden="true"><span></span><span></span></div></div>
 <div class="auth-card glass-panel"><h2>Log in</h2><p class="text-muted">Enter your OarNote account details.</p><?php if ($error) display_message('error', $error); ?>
  <form method="post" novalidate><input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
   <div class="form-group"><label class="form-label" for="email">Email address</label><input id="email" name="email" type="email" autocomplete="email" required value="<?php echo htmlspecialchars($email); ?>"></div>
   <div class="form-group"><label class="form-label" for="password">Password</label><input id="password" name="password" type="password" autocomplete="current-password" required></div>
   <button class="btn btn-primary btn-block" type="submit">Log in</button>
  </form><p class="auth-switch">New to OarNote? <a href="/auth/register.php">Create an account</a></p>
 </div>
</section>
<?php require '../includes/footer.php'; ?>
