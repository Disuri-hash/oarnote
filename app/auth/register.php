<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/messages.php';
redirect_if_logged_in();

$values = ['first_name' => '', 'last_name' => '', 'email' => '', 'role' => 'rower'];
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (array_keys($values) as $key) $values[$key] = trim($_POST[$key] ?? $values[$key]);
    $values['email'] = strtolower($values['email']);
    $password = $_POST['password'] ?? '';
    $confirmation = $_POST['password_confirmation'] ?? '';
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) $errors[] = 'Your form expired. Please try again.';
    if ($values['first_name'] === '' || strlen($values['first_name']) > 100) $errors[] = 'Enter a valid first name.';
    if ($values['last_name'] === '' || strlen($values['last_name']) > 100) $errors[] = 'Enter a valid last name.';
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (!in_array($values['role'], ['coach', 'rower'], true)) $errors[] = 'Choose a valid account type.';
    if (strlen($password) < 8) $errors[] = 'Your password must contain at least 8 characters.';
    if ($password !== $confirmation) $errors[] = 'The passwords do not match.';
    if (!$errors) {
        require '../config/database.php';
        $statement = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $statement->execute([$values['email']]);
        if ($statement->fetch()) {
            $errors[] = 'An account already exists for that email address.';
        } else {
            $statement = $pdo->prepare('INSERT INTO users (email, password_hash, first_name, last_name, role) VALUES (?, ?, ?, ?, ?)');
            $statement->execute([$values['email'], password_hash($password, PASSWORD_DEFAULT), $values['first_name'], $values['last_name'], $values['role']]);
            login_user(['id' => $pdo->lastInsertId(), 'email' => $values['email'], 'first_name' => $values['first_name'], 'last_name' => $values['last_name'], 'role' => $values['role']]);
            set_session_message('success', 'Your OarNote account is ready.');
            header('Location: /pages/dashboard.php');
            exit;
        }
    }
}
$page_title = 'Create account - OarNote';
require '../includes/header.php';
?>
<section class="auth-layout">
 <div class="auth-intro"><p class="eyebrow">Join the crew</p><h1>Better training starts here.</h1><p>Create a secure workspace for your rowing sessions, results, and feedback.</p><div class="auth-oar" aria-hidden="true"><span></span><span></span></div></div>
 <div class="auth-card glass-panel"><h2>Create account</h2><p class="text-muted">Set up your OarNote profile.</p><?php if ($errors): ?><div class="alert alert-error" role="alert"><span class="alert-icon">!</span><div><?php foreach ($errors as $item): ?><div><?php echo htmlspecialchars($item); ?></div><?php endforeach; ?></div></div><?php endif; ?>
  <form method="post" novalidate><input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
   <div class="form-row"><div class="form-group"><label class="form-label" for="first_name">First name</label><input id="first_name" name="first_name" type="text" autocomplete="given-name" maxlength="100" required value="<?php echo htmlspecialchars($values['first_name']); ?>"></div><div class="form-group"><label class="form-label" for="last_name">Last name</label><input id="last_name" name="last_name" type="text" autocomplete="family-name" maxlength="100" required value="<?php echo htmlspecialchars($values['last_name']); ?>"></div></div>
   <div class="form-group"><label class="form-label" for="email">Email address</label><input id="email" name="email" type="email" autocomplete="email" required value="<?php echo htmlspecialchars($values['email']); ?>"></div>
   <div class="form-group"><label class="form-label" for="role">I am a</label><select id="role" name="role"><option value="rower" <?php echo $values['role'] === 'rower' ? 'selected' : ''; ?>>Rower</option><option value="coach" <?php echo $values['role'] === 'coach' ? 'selected' : ''; ?>>Coach</option></select></div>
   <div class="form-row"><div class="form-group"><label class="form-label" for="password">Password</label><input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required></div><div class="form-group"><label class="form-label" for="password_confirmation">Confirm password</label><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required></div></div>
   <button class="btn btn-primary btn-block" type="submit">Create account</button>
  </form><p class="auth-switch">Already have an account? <a href="/auth/login.php">Log in</a></p>
 </div>
</section>
<?php require '../includes/footer.php'; ?>
