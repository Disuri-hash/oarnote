<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/messages.php';
require_once '../includes/session-validation.php';
require_role('coach');
require '../config/database.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { http_response_code(404); exit('Session not found.'); }
$statement = $pdo->prepare('SELECT * FROM sessions WHERE id = ? AND coach_id = ? LIMIT 1');
$statement->execute([$id, (int) $_SESSION['user_id']]);
$session_data = $statement->fetch();
if (!$session_data) { http_response_code(404); exit('Session not found.'); }
$segment_statement=$pdo->prepare('SELECT * FROM workout_segments WHERE session_id=? ORDER BY segment_order,id');$segment_statement->execute([$id]);$segments=$segment_statement->fetchAll();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $session_data = session_form_values($_POST);
    $segments = session_segments($_POST);
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) $errors[] = 'Your form expired. Please try again.';
    $errors = array_merge($errors, validate_session_form($session_data, $segments));
    if (!$errors) {
        $pdo->beginTransaction();
        $statement = $pdo->prepare('UPDATE sessions SET date=?,session_type=?,workout_format=?,status=?,purpose=?,boat_class=?,title=?,coach_notes=? WHERE id=? AND coach_id=?');
        $statement->execute([$session_data['date'],$session_data['session_type'],$session_data['session_type']==='Erg'?$session_data['workout_format']:null,$session_data['status'],nullable_session_value($session_data['purpose']),$session_data['session_type']==='Water'?nullable_session_value($session_data['boat_class']):null,$session_data['title'],nullable_session_value($session_data['coach_notes']),$id,(int)$_SESSION['user_id']]);
        $pdo->prepare('DELETE FROM workout_segments WHERE session_id=?')->execute([$id]);
        save_segments($pdo,(int)$id,$segments);
        $pdo->commit();
        set_session_message('success', 'Training session updated successfully.');
        header('Location: /pages/dashboard.php');
        exit;
    }
}
$page_title = 'Edit session - OarNote';
require '../includes/header.php';
?>
<header class="page-header"><div><p class="eyebrow">Coach workspace</p><h1>Edit session</h1><p>Update the training plan and crew details.</p></div><a href="/pages/dashboard.php" class="btn btn-secondary">Cancel</a></header>
<div class="form-shell card"><?php if ($errors): ?><div class="alert alert-error" role="alert"><span class="alert-icon">!</span><div><?php foreach ($errors as $error): ?><div><?php echo htmlspecialchars($error); ?></div><?php endforeach; ?></div></div><?php endif; ?><form method="post" novalidate><input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>"><?php require '../includes/session-form.php'; ?><div class="form-actions"><a href="/pages/dashboard.php" class="btn btn-secondary">Cancel</a><button type="submit" class="btn btn-primary">Save changes</button></div></form></div>
<?php require '../includes/footer.php'; ?>
