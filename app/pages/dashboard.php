<?php
session_start();
require_once '../includes/auth.php';
require_login();
require '../config/database.php';

$user_id = (int) $_SESSION['user_id'];
$is_coach = get_user_role() === 'coach';
if ($is_coach) {
    $statement = $pdo->prepare('SELECT s.*, COUNT(r.id) AS result_count FROM sessions s LEFT JOIN results r ON r.session_id = s.id WHERE s.coach_id = ? GROUP BY s.id ORDER BY s.date DESC, s.id DESC');
    $statement->execute([$user_id]);
} else {
    $statement = $pdo->prepare("SELECT s.*, CONCAT(u.first_name, ' ', u.last_name) AS coach_name, COUNT(r.id) AS result_count FROM sessions s JOIN users u ON u.id = s.coach_id LEFT JOIN results r ON r.session_id = s.id AND r.rower_id = ? GROUP BY s.id ORDER BY s.date DESC, s.id DESC");
    $statement->execute([$user_id]);
}
$sessions = $statement->fetchAll();
$upcoming = 0;
$completed = 0;
$next_session = null;
$today = date('Y-m-d');
foreach ($sessions as $training_session) {
    if ($training_session['status'] === 'upcoming') {
        $upcoming++;
        if ($next_session === null || $training_session['date'] < $next_session['date']) $next_session = $training_session;
    } elseif ($training_session['status'] === 'completed') {
        $completed++;
    }
}

$page_title = 'Sessions - OarNote';
require '../includes/header.php';
?>
<header class="page-header">
 <div><p class="eyebrow"><?php echo $is_coach ? 'Coach workspace' : 'Training log'; ?></p><h1>Sessions</h1><p><?php echo $is_coach ? 'Plan and manage training for your crew.' : 'Review your crew training and record your results.'; ?></p></div>
 <?php if ($is_coach): ?><a href="/pages/session-create.php" class="btn btn-primary">Create session</a><?php endif; ?>
</header>
<div class="grid grid-3">
 <div class="stat-card stat-primary"><div class="stat-value"><?php echo $upcoming; ?></div><div class="stat-label">Upcoming</div></div>
 <div class="stat-card stat-success"><div class="stat-value"><?php echo $completed; ?></div><div class="stat-label">Past sessions</div></div>
 <div class="stat-card stat-warning"><div class="stat-value stat-date"><?php echo $next_session ? date('M j', strtotime($next_session['date'])) : '&mdash;'; ?></div><div class="stat-label">Next session</div></div>
</div>

<?php if (!$sessions): ?>
<div class="card session-empty"><div class="empty-state"><div class="empty-icon" aria-hidden="true">+</div><h2><?php echo $is_coach ? 'Plan your first session' : 'No sessions yet'; ?></h2><p class="text-muted"><?php echo $is_coach ? 'Create a training session and give your crew a clear plan for the next workout.' : 'Your coach has not added any training sessions yet.'; ?></p><?php if ($is_coach): ?><a href="/pages/session-create.php" class="btn btn-primary">Create session</a><?php endif; ?></div></div>
<?php else: ?>
<section class="session-section"><div class="section-heading"><div><p class="eyebrow">Training programme</p><h2>All sessions</h2></div><p><?php echo count($sessions); ?> session<?php echo count($sessions) === 1 ? '' : 's'; ?> in your workspace.</p></div>
 <div class="session-list">
 <?php foreach ($sessions as $training_session): ?>
  <article class="session-card card">
   <div class="session-date"><span><?php echo date('M', strtotime($training_session['date'])); ?></span><strong><?php echo date('j', strtotime($training_session['date'])); ?></strong><small><?php echo date('Y', strtotime($training_session['date'])); ?></small></div>
    <div class="session-main"><div class="session-title-row"><div><span class="type-pill"><?php echo htmlspecialchars($training_session['session_type']); ?></span><?php if($training_session['purpose']): ?> <span class="type-pill"><?= htmlspecialchars($training_session['purpose']) ?></span><?php endif ?><?php if(!$is_coach&&(int)$training_session['result_count']>0): ?> <span class="type-pill submitted-pill">Submitted</span><?php endif ?><h3><?php echo htmlspecialchars($training_session['title'] ?: $training_session['session_type'] . ' session'); ?></h3></div><span class="status-pill status-<?= htmlspecialchars($training_session['status']) ?>"><?= htmlspecialchars($training_session['status']==='open'?'Open for Results':ucfirst($training_session['status'])) ?></span></div>
    <?php if ($training_session['coach_notes']): ?><p><?php echo nl2br(htmlspecialchars($training_session['coach_notes'])); ?></p><?php endif; ?>
    <div class="session-meta"><?php if ($training_session['workout_format']): ?><span><strong><?php echo htmlspecialchars(ucwords(str_replace('_',' ',$training_session['workout_format']))); ?></strong> format</span><?php endif; ?><?php if($training_session['boat_class']): ?><span><strong><?= htmlspecialchars($training_session['boat_class']) ?></strong> boat</span><?php endif ?><?php if (!$is_coach): ?><span><strong><?php echo htmlspecialchars($training_session['coach_name']); ?></strong> coach</span><?php endif; ?><?php if($is_coach): ?><span><strong><?= (int)$training_session['result_count'] ?></strong> submissions</span><?php endif ?></div>
   </div>
   <div class="session-actions"><a class="btn btn-primary btn-small" href="/pages/session-view.php?id=<?= (int)$training_session['id'] ?>">View</a><?php if ($is_coach): ?><a class="btn btn-secondary btn-small" href="/pages/session-edit.php?id=<?php echo (int) $training_session['id']; ?>">Edit</a><form method="post" action="/pages/session-delete.php" onsubmit="return confirm('Delete this session and all associated results?');"><input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int) $training_session['id']; ?>"><button type="submit" class="btn btn-danger btn-small">Delete</button></form><?php endif; ?></div>
  </article>
 <?php endforeach; ?>
 </div>
</section>
<?php endif; ?>
<?php require '../includes/footer.php'; ?>
