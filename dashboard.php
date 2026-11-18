<?php
$title = 'Dashboard';
require 'includes/header.php';

$where = is_admin() ? '' : 'WHERE student_id = ' . uid();
$row = $pdo->query("SELECT COUNT(*) total,
    SUM(status='Pending') pending,
    SUM(status='In Progress') progress,
    SUM(status='Completed') done,
    COALESCE(ROUND(AVG(progress)),0) avgp,
    SUM(status='Completed' AND (feedback IS NULL OR TRIM(feedback)='')) review,
    SUM(feedback IS NOT NULL AND TRIM(feedback)<>'') feedback_count
    FROM projects $where")->fetch();
$total = (int)$row['total'];
$pending = (int)$row['pending'];
$inprog = (int)$row['progress'];
$done = (int)$row['done'];
$avg = (int)$row['avgp'];
$review = (int)$row['review'];
$feedbackCount = (int)$row['feedback_count'];
$students = is_admin() ? (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn() : null;
$hour = (int)date('G');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
$displayName = is_admin() ? trim($_SESSION['user']['name']) : explode(' ', trim($_SESSION['user']['name']))[0];

$recent = $pdo->query("SELECT p.*, u.name sname FROM projects p JOIN users u ON u.id=p.student_id " .
    (is_admin() ? '' : 'WHERE p.student_id = ' . uid()) . " ORDER BY p.created_at DESC LIMIT 6")->fetchAll();
?>
<section class="welcome dashboard-hero">
  <div class="dashboard-hero-copy">
    <span class="dashboard-eyebrow"><i class="bi bi-stars"></i> YOUR ACADEMIC WORKSPACE</span>
    <h2><?= e($greeting) ?>, <?= e($displayName) ?></h2>
    <p><?= is_admin() ? 'Your overview is ready. Keep student projects moving forward.' : 'A clear view of your projects, progress and guide feedback.' ?></p>
    <div class="dashboard-hero-actions">
      <a class="btn btn-light" href="project_form.php"><i class="bi bi-plus-lg"></i> New Project</a>
      <a class="dashboard-secondary-link" href="projects.php">Explore projects <i class="bi bi-arrow-up-right"></i></a>
    </div>
  </div>
  <div class="dashboard-pulse">
    <div class="dashboard-pulse-heading">
      <span class="dashboard-pulse-icon"><i class="bi bi-activity"></i></span>
      <div><small>WORKSPACE PULSE</small><b><?= is_admin() ? 'Project momentum' : 'Your momentum' ?></b></div>
    </div>
    <div class="dashboard-pulse-body">
      <div class="dashboard-progress-ring" style="--p: <?= $avg ?>"><span><?= $avg ?><small>%</small></span></div>
      <div class="dashboard-pulse-details">
        <b><?= $avg >= 75 ? 'Great momentum' : ($avg >= 40 ? 'Steady progress' : 'Room to grow') ?></b>
        <span>Average completion</span>
        <?php if (is_admin()): ?>
          <span class="dashboard-pulse-note"><i class="bi bi-people-fill"></i> <?= $students ?> registered <?= $students === 1 ? 'student' : 'students' ?></span>
          <span class="dashboard-pulse-note"><i class="bi bi-chat-square-dots-fill"></i> <?= $review ?> awaiting feedback</span>
        <?php else: ?>
          <span class="dashboard-pulse-note"><i class="bi bi-folder2-open"></i> <?= $total ?> <?= $total === 1 ? 'project' : 'projects' ?> in your workspace</span>
          <span class="dashboard-pulse-note"><i class="bi bi-chat-square-dots-fill"></i> <?= $feedbackCount ?> feedback <?= $feedbackCount === 1 ? 'note' : 'notes' ?> received</span>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<div class="stats dashboard-stats">
  <a class="stat dashboard-stat s1" href="projects.php">
    <i class="bi bi-folder-fill"></i><div><h3 data-count="<?= $total ?>">0</h3><span>Total Projects</span><small>Across your workspace</small></div>
    <i class="bi bi-arrow-up-right stat-arrow" aria-hidden="true"></i>
  </a>
  <a class="stat dashboard-stat s2" href="projects.php?status=Pending">
    <i class="bi bi-hourglass-split"></i><div><h3 data-count="<?= $pending ?>">0</h3><span>Pending</span><small>Ready to get started</small></div>
    <i class="bi bi-arrow-up-right stat-arrow" aria-hidden="true"></i>
  </a>
  <a class="stat dashboard-stat s3" href="projects.php?status=In+Progress">
    <i class="bi bi-lightning-charge-fill"></i><div><h3 data-count="<?= $inprog ?>">0</h3><span>In Progress</span><small>Currently underway</small></div>
    <i class="bi bi-arrow-up-right stat-arrow" aria-hidden="true"></i>
  </a>
  <a class="stat dashboard-stat s4" href="projects.php?status=Completed">
    <i class="bi bi-patch-check-fill"></i><div><h3 data-count="<?= $done ?>">0</h3><span>Completed</span><small>Milestones reached</small></div>
    <i class="bi bi-arrow-up-right stat-arrow" aria-hidden="true"></i>
  </a>
</div>

<div class="grid-2 dashboard-insights">
  <section class="card dashboard-card">
    <div class="dashboard-section-heading">
      <div><span class="dashboard-section-kicker">AT A GLANCE</span><h3 class="card-title">Project overview</h3></div>
      <a class="dashboard-text-link" href="projects.php">View all <i class="bi bi-arrow-right"></i></a>
    </div>
    <?php if ($total): ?>
      <div class="chart-wrap dashboard-chart-wrap"><canvas id="statusChart" data-values="<?= $pending ?>,<?= $inprog ?>,<?= $done ?>" data-total="<?= $total ?>" role="img" aria-label="Project status: <?= $pending ?> pending, <?= $inprog ?> in progress, <?= $done ?> completed"></canvas></div>
      <p class="dashboard-chart-hint"><i class="bi bi-hand-index-thumb"></i> Select a status in the chart legend to focus on it.</p>
    <?php else: ?>
      <div class="dashboard-empty-state"><span><i class="bi bi-bar-chart-line"></i></span><b>Your project story starts here</b><p>Add your first project to see progress insights and status breakdowns.</p><a class="btn btn-primary sm" href="project_form.php"><i class="bi bi-plus-lg"></i> Create a project</a></div>
    <?php endif; ?>
  </section>

  <section class="card dashboard-card dashboard-progress-card">
    <div class="dashboard-section-heading">
      <div><span class="dashboard-section-kicker">KEEP THE MOMENTUM</span><h3 class="card-title">Overall progress</h3></div>
      <span class="dashboard-soft-icon"><i class="bi bi-graph-up-arrow"></i></span>
    </div>
    <div class="ring dashboard-main-ring" style="--p: <?= $avg ?>"><span><?= $avg ?><small>%</small></span></div>
    <p class="dashboard-progress-caption"><?= $total ? 'Average completion across ' . $total . ' ' . ($total === 1 ? 'project' : 'projects') . '.' : 'Your progress insights will appear once you add a project.' ?></p>
    <div class="dashboard-progress-summary">
      <span><i class="bi bi-circle-fill status-dot-pending"></i> Pending <b><?= $pending ?></b></span>
      <span><i class="bi bi-circle-fill status-dot-progress"></i> In progress <b><?= $inprog ?></b></span>
      <span><i class="bi bi-circle-fill status-dot-done"></i> Completed <b><?= $done ?></b></span>
    </div>
    <a class="dashboard-progress-link" href="<?= $total ? 'projects.php' : 'project_form.php' ?>"><?= $total ? 'See project details' : 'Get started' ?> <i class="bi bi-arrow-right"></i></a>
  </section>
</div>

<section class="card dashboard-recent-card">
  <div class="dashboard-section-heading dashboard-recent-heading">
    <div><span class="dashboard-section-kicker">LATEST UPDATES</span><h3 class="card-title">Recent projects</h3></div>
    <a class="btn btn-ghost btn.sm" href="projects.php">All projects <i class="bi bi-arrow-right"></i></a>
  </div>
  <div class="table-wrap">
  <table>
    <thead><tr><th>Title</th><?php if (is_admin()): ?><th>Student</th><?php endif; ?><th>Status</th><th>Progress</th></tr></thead>
    <tbody>
    <?php foreach ($recent as $p): ?>
      <tr>
        <td><a href="project_view.php?id=<?= $p['id'] ?>"><b><?= e($p['title']) ?></b></a></td>
        <?php if (is_admin()): ?><td><?= e($p['sname']) ?></td><?php endif; ?>
        <td><?= badge($p['status']) ?></td>
        <td><div class="bar"><i style="width:<?= (int)$p['progress'] ?>%"></i></div><small><?= (int)$p['progress'] ?>%</small></td>
      </tr>
    <?php endforeach; if (!$recent): ?>
      <tr><td colspan="4" class="center muted">Nothing here yet — create your first project.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
  </div>
  <?php if ($recent): ?><a class="dashboard-mobile-all" href="projects.php">View all projects <i class="bi bi-arrow-right"></i></a><?php endif; ?>
</section>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<?php require 'includes/footer.php'; ?>
