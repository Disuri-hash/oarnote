<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/messages.php';
require_once '../includes/session-validation.php';
require_role('coach');

$session_data = session_form_values([]);
$session_data['date'] = date('Y-m-d');
$session_data['session_type'] = 'Erg';
$segments = [];
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $session_data = session_form_values($_POST);
    $segments = session_segments($_POST);
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) $errors[] = 'Your form expired. Please try again.';
    $errors = array_merge($errors, validate_session_form($session_data, $segments));
    if (!$errors) {
        require '../config/database.php';
        $pdo->beginTransaction();
        $statement = $pdo->prepare('INSERT INTO sessions (coach_id,date,session_type,workout_format,status,purpose,boat_class,title,coach_notes) VALUES (?,?,?,?,?,?,?,?,?)');
        $statement->execute([(int)$_SESSION['user_id'],$session_data['date'],$session_data['session_type'],$session_data['session_type']==='Erg'?$session_data['workout_format']:null,$session_data['status'],nullable_session_value($session_data['purpose']),$session_data['session_type']==='Water'?nullable_session_value($session_data['boat_class']):null,$session_data['title'],nullable_session_value($session_data['coach_notes'])]);
        save_segments($pdo, (int)$pdo->lastInsertId(), $segments);
        $pdo->commit();
        set_session_message('success', 'Training session created successfully.');
        header('Location: /pages/dashboard.php');
        exit;
    }
}
$page_title = 'Create session - OarNote';
require '../includes/header.php';
?>
<header class="page-header"><div><p class="eyebrow">Coach workspace</p><h1>Create session</h1><p>Give your crew a clear plan for their next workout.</p></div><a href="/pages/dashboard.php" class="btn btn-secondary">Cancel</a></header>
<div class="form-shell card"><?php if ($errors): ?><div class="alert alert-error" role="alert"><span class="alert-icon">!</span><div><?php foreach ($errors as $error): ?><div><?php echo htmlspecialchars($error); ?></div><?php endforeach; ?></div></div><?php endif; ?><form method="post" novalidate><input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>"><?php require '../includes/session-form.php'; ?><div class="form-actions"><a href="/pages/dashboard.php" class="btn btn-secondary">Cancel</a><button type="submit" class="btn btn-primary">Create session</button></div></form></div>
<?php require '../includes/footer.php'; ?>
