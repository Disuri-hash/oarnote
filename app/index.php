<?php
session_start();
$page_title = "Dashboard - OarNote";
require 'includes/header.php';
require 'config/database.php';
require 'includes/components.php';
?>
<section class="hero">
 <div><p class="eyebrow">Built for better performance</p><h1>Every stroke. <span class="gradient-text">Every session.</span> One place.</h1><p class="hero-copy">Plan training, capture results, and keep your whole crew moving in the same direction with a workspace designed for rowing.</p><div class="hero-actions"><?php if (is_logged_in()): ?><a class="btn btn-primary" href="/pages/dashboard.php">Open dashboard</a><?php else: ?><a class="btn btn-primary" href="/auth/register.php">Get started</a><?php endif; ?><a class="btn btn-secondary" href="#crew">Explore OarNote</a></div></div>
 <div class="hero-panel glass-panel" aria-label="Rowing training preview"><div class="crossed-oars" aria-hidden="true"><span></span><span></span></div><div class="panel-top"><span class="panel-label">On the water</span><span class="status-pill">Crew ready</span></div><div class="workout-card"><span class="panel-label">SATURDAY &middot; 06:00</span><h3>Technical endurance</h3><div class="workout-meta"><div><strong>12 km</strong><span>Distance</span></div><div><strong>18-22</strong><span>Rate</span></div><div><strong>8+</strong><span>Boat</span></div></div></div><div class="water-lines" aria-hidden="true"><i></i><i></i><i></i></div></div>
</section>
<section id="crew"><div class="section-heading"><div><p class="eyebrow">A smarter boathouse</p><h2>Made for the whole crew</h2></div><p>Less admin, clearer feedback, and a shared view of every training block.</p></div><div class="grid grid-3">
 <article class="card feature-card"><div class="feature-icon">01</div><h3>Plan with clarity</h3><p>Build erg and water sessions, set targets, and make every workout easy to understand.</p></article><article class="card feature-card"><div class="feature-icon">02</div><h3>Track progress</h3><p>Keep results and personal performance together so improvements never get lost.</p></article><article class="card feature-card"><div class="feature-icon">03</div><h3>Coach together</h3><p>Give focused feedback and keep rowers, coaches, and coxswains aligned.</p></article>
</div></section>
<section class="grid grid-2 role-grid"><div class="card"><div class="card-header">For coaches</div><div class="card-body"><ul class="role-list"><li><span class="check">&#10003;</span>Create and manage training sessions</li><li><span class="check">&#10003;</span>Review submissions and results</li><li><span class="check">&#10003;</span>Give clear performance feedback</li></ul></div></div><div class="card"><div class="card-header">For rowers</div><div class="card-body"><ul class="role-list"><li><span class="check">&#10003;</span>See every upcoming session</li><li><span class="check">&#10003;</span>Record results in seconds</li><li><span class="check">&#10003;</span>Follow personal progress over time</li></ul></div></div></section>
<?php require 'includes/footer.php'; ?>
