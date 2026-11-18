<?php
require_once 'config.php';
require_login();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$p = ['title' => '', 'description' => '', 'tech_stack' => '', 'guide' => '', 'status' => 'Pending', 'progress' => 0, 'start_date' => '', 'end_date' => ''];

if ($id) {
    $st = $pdo->prepare("SELECT * FROM projects WHERE id=?");
    $st->execute([$id]);
    $found = $st->fetch();
    if (!$found || (!is_admin() && $found['student_id'] != uid())) { flash('Project not found.', 'danger'); header('Location: projects.php'); exit; }
    $p = $found;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $d = [
        trim($_POST['title'] ?? ''), trim($_POST['description'] ?? ''), trim($_POST['tech_stack'] ?? ''), trim($_POST['guide'] ?? ''),
        in_array($_POST['status'] ?? '', ['Pending', 'In Progress', 'Completed']) ? $_POST['status'] : 'Pending',
        max(0, min(100, (int)($_POST['progress'] ?? 0))),
        ($_POST['start_date'] ?? '') ?: null, ($_POST['end_date'] ?? '') ?: null,
    ];
    if ($d[0] === '') $error = 'Project title is required.';
    elseif ($d[6] && $d[7] && $d[7] < $d[6]) $error = 'End date cannot be before start date.';
    else {
        if ($d[4] === 'Completed') $d[5] = 100;
        if ($id) {
            $pdo->prepare("UPDATE projects SET title=?,description=?,tech_stack=?,guide=?,status=?,progress=?,start_date=?,end_date=? WHERE id=?")
                ->execute([...$d, $id]);
            flash('Project updated successfully.');
        } else {
            $pdo->prepare("INSERT INTO projects(title,description,tech_stack,guide,status,progress,start_date,end_date,student_id) VALUES(?,?,?,?,?,?,?,?,?)")
                ->execute([...$d, uid()]);
            flash('Project created successfully.');
        }
        header('Location: projects.php'); exit;
    }
    $p = array_merge($p, ['title' => $d[0], 'description' => $d[1], 'tech_stack' => $d[2], 'guide' => $d[3], 'status' => $d[4], 'progress' => $d[5], 'start_date' => $d[6], 'end_date' => $d[7]]);
}

$title = $id ? 'Edit Project' : 'New Project';
require 'includes/header.php';
?>
<div class="card form-card">
  <?php if ($error): ?><div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill"></i> <?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= csrf() ?>"><input type="hidden" name="id" value="<?= $id ?>">
    <div class="form-grid">
      <div class="full"><label>Project title *</label><input name="title" value="<?= e($p['title']) ?>" required></div>
      <div class="full"><label>Description</label><textarea name="description" rows="4"><?= e($p['description']) ?></textarea></div>
      <div><label>Technology stack</label><input name="tech_stack" value="<?= e($p['tech_stack']) ?>" placeholder="PHP, MySQL, JavaScript"></div>
      <div><label>Project guide</label><input name="guide" value="<?= e($p['guide']) ?>" placeholder="Prof. Name"></div>
      <div><label>Start date</label><input type="date" name="start_date" value="<?= e($p['start_date']) ?>"></div>
      <div><label>End date</label><input type="date" name="end_date" value="<?= e($p['end_date']) ?>"></div>
      <div><label>Status</label>
        <select name="status"><?php foreach (['Pending', 'In Progress', 'Completed'] as $s): ?><option <?= $p['status'] == $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?></select>
      </div>
      <div><label>Progress: <b id="pv"><?= (int)$p['progress'] ?></b>%</label>
        <input type="range" name="progress" min="0" max="100" value="<?= (int)$p['progress'] ?>" oninput="pv.textContent=this.value">
      </div>
    </div>
    <div class="form-actions">
      <a class="btn btn-ghost" href="projects.php">Cancel</a>
      <button class="btn btn-primary"><i class="bi bi-check-lg"></i> <?= $id ? 'Update' : 'Save' ?> Project</button>
    </div>
  </form>
</div>
<?php require 'includes/footer.php'; ?>
