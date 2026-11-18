<?php
require_once 'config.php';
require_login();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$st = $pdo->prepare("SELECT p.*, u.name sname, u.email semail FROM projects p JOIN users u ON u.id=p.student_id WHERE p.id=?");
$st->execute([$id]);
$p = $st->fetch();
if (!$p || (!is_admin() && $p['student_id'] != uid())) { flash('Project not found.', 'danger'); header('Location: projects.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_admin()) {
    check_csrf();
    $grade = trim($_POST['grade'] ?? '');
    $pdo->prepare("UPDATE projects SET feedback=?, grade=? WHERE id=?")
        ->execute([trim($_POST['feedback'] ?? ''), $grade === '' ? null : substr($grade, 0, 5), $id]);
    flash('Feedback saved.');
    header("Location: project_view.php?id=$id"); exit;
}

$title = 'Project Details';
require 'includes/header.php';
?>
<div class="grid-2 wide-left">
  <div class="card">
    <div class="card-head">
      <h2 class="proj-title"><?= e($p['title']) ?></h2>
      <?= badge($p['status']) ?>
    </div>
    <p class="muted"><?= nl2br(e($p['description'] ?: 'No description provided.')) ?></p>
    <div class="info-grid">
      <div><small>Student</small><b><?= e($p['sname']) ?></b></div>
      <div><small>Guide</small><b><?= e($p['guide'] ?: '—') ?></b></div>
      <div><small>Technology</small><b><?= e($p['tech_stack'] ?: '—') ?></b></div>
      <div><small>Start</small><b><?= e($p['start_date'] ?: '—') ?></b></div>
      <div><small>End</small><b><?= e($p['end_date'] ?: '—') ?></b></div>
      <div><small>Grade</small><b><?= e($p['grade'] ?: 'Not graded') ?></b></div>
    </div>
    <label>Progress</label>
    <div class="bar big"><i style="width:<?= (int)$p['progress'] ?>%"></i></div>
    <small><?= (int)$p['progress'] ?>% complete</small>
    <div class="form-actions left">
      <a class="btn btn-ghost" href="projects.php"><i class="bi bi-arrow-left"></i> Back</a>
      <a class="btn btn-primary" href="project_form.php?id=<?= $p['id'] ?>"><i class="bi bi-pencil-square"></i> Edit</a>
    </div>
  </div>
  <div class="card">
    <h3 class="card-title"><i class="bi bi-chat-square-text"></i> Guide Feedback</h3>
    <?php if (is_admin()): ?>
      <form method="post">
        <input type="hidden" name="csrf" value="<?= csrf() ?>"><input type="hidden" name="id" value="<?= $p['id'] ?>">
        <label>Feedback</label><textarea name="feedback" rows="5"><?= e($p['feedback']) ?></textarea>
        <label>Grade</label><input name="grade" maxlength="5" value="<?= e($p['grade']) ?>" placeholder="A+, B, 85...">
        <button class="btn btn-primary w-100"><i class="bi bi-send"></i> Save Feedback</button>
      </form>
    <?php else: ?>
      <p><?= $p['feedback'] ? nl2br(e($p['feedback'])) : '<span class="muted">No feedback from your guide yet.</span>' ?></p>
    <?php endif; ?>
  </div>
</div>
<?php require 'includes/footer.php'; ?>
