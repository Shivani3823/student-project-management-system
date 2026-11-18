<?php
require_once 'config.php';
require_login();
if (!is_admin()) { flash('Admin access only.', 'danger'); header('Location: dashboard.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $pdo->prepare("DELETE FROM users WHERE id=? AND role='student'")->execute([(int)$_POST['id']]);
    flash('Student removed.');
    header('Location: students.php'); exit;
}
$rows = $pdo->query("SELECT u.*, COUNT(p.id) pc FROM users u LEFT JOIN projects p ON p.student_id=u.id WHERE u.role='student' GROUP BY u.id ORDER BY u.created_at DESC")->fetchAll();

$title = 'Students';
require 'includes/header.php';
?>
<div class="card">
  <div class="student-heading">
    <div>
      <span class="eyebrow">PEOPLE DIRECTORY</span>
      <h3 class="card-title">Registered Students</h3>
    </div>
    <span class="results-count" id="studentSearchCount"><?= count($rows) ?> students</span>
  </div>
  <div class="student-tools">
    <div class="input-icon student-search">
      <i class="bi bi-search" aria-hidden="true"></i>
      <input type="search" data-table-search="studentRows" placeholder="Search students by name or email" aria-label="Search students">
      <kbd>Ctrl K</kbd>
    </div>
  </div>
  <div class="table-wrap">
  <table>
    <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Projects</th><th>Joined</th><th></th></tr></thead>
    <tbody id="studentRows">
    <?php foreach ($rows as $i => $r): ?>
      <tr data-filter-item>
        <td><?= $i + 1 ?></td>
        <td><div class="who"><span class="avatar sm"><?= strtoupper(substr($r['name'], 0, 1)) ?></span><b><?= e($r['name']) ?></b></div></td>
        <td><?= e($r['email']) ?></td>
        <td><span class="badge b-progress"><?= $r['pc'] ?></span></td>
        <td><?= date('d M Y', strtotime($r['created_at'])) ?></td>
        <td class="actions">
          <form method="post" onsubmit="return confirm('Remove this student and all their projects?')">
            <input type="hidden" name="csrf" value="<?= csrf() ?>"><input type="hidden" name="id" value="<?= $r['id'] ?>">
            <button class="danger"><i class="bi bi-trash"></i></button>
          </form>
        </td>
      </tr>
    <?php endforeach; if (!$rows): ?>
      <tr><td colspan="6" class="center muted">No students registered yet.</td></tr>
    <?php endif; ?>
      <tr data-search-empty hidden><td colspan="6" class="center muted">No students match your search.</td></tr>
    </tbody>
  </table>
  </div>
</div>
<?php require 'includes/footer.php'; ?>
