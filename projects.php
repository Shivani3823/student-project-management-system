<?php
$title = 'Projects';
require 'includes/header.php';

$q = trim($_GET['q'] ?? '');
$st = $_GET['status'] ?? '';
$sql = "SELECT p.*, u.name sname FROM projects p JOIN users u ON u.id=p.student_id WHERE 1=1";
$args = [];
if (!is_admin()) { $sql .= " AND p.student_id=?"; $args[] = uid(); }
if ($q !== '') { $sql .= " AND (p.title LIKE ? OR p.tech_stack LIKE ? OR u.name LIKE ?)"; array_push($args, "%$q%", "%$q%", "%$q%"); }
if (in_array($st, ['Pending', 'In Progress', 'Completed'])) { $sql .= " AND p.status=?"; $args[] = $st; }
$sql .= " ORDER BY p.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($args);
$rows = $stmt->fetchAll();
?>
<div class="card">
  <div class="card-head">
    <form class="filters" method="get">
      <div class="input-icon sm"><i class="bi bi-search"></i><input name="q" value="<?= e($q) ?>" placeholder="Search title, tech, student..."></div>
      <select name="status" onchange="this.form.submit()">
        <option value="">All status</option>
        <?php foreach (['Pending', 'In Progress', 'Completed'] as $s): ?>
          <option <?= $st == $s ? 'selected' : '' ?>><?= $s ?></option>
        <?php endforeach; ?>
      </select>
      <button class="btn btn-primary sm">Search</button>
    </form>
    <a class="btn btn-primary sm" href="project_form.php"><i class="bi bi-plus-lg"></i> New</a>
  </div>
  <div class="table-wrap">
  <table>
    <thead><tr><th>#</th><th>Title</th><?php if (is_admin()): ?><th>Student</th><?php endif; ?><th>Tech</th><th>Status</th><th>Progress</th><th>Grade</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $i => $p): ?>
      <tr>
        <td><?= $i + 1 ?></td>
        <td><b><?= e($p['title']) ?></b><br><small class="muted"><?= e($p['guide']) ?></small></td>
        <?php if (is_admin()): ?><td><?= e($p['sname']) ?></td><?php endif; ?>
        <td><?= e($p['tech_stack']) ?></td>
        <td><?= badge($p['status']) ?></td>
        <td><div class="bar"><i style="width:<?= (int)$p['progress'] ?>%"></i></div><small><?= (int)$p['progress'] ?>%</small></td>
        <td><?= $p['grade'] ? '<span class="grade">' . e($p['grade']) . '</span>' : '—' ?></td>
        <td class="actions">
          <a href="project_view.php?id=<?= $p['id'] ?>" title="View"><i class="bi bi-eye"></i></a>
          <a href="project_form.php?id=<?= $p['id'] ?>" title="Edit"><i class="bi bi-pencil-square"></i></a>
          <form method="post" action="project_delete.php" onsubmit="return confirm('Delete this project?')">
            <input type="hidden" name="csrf" value="<?= csrf() ?>"><input type="hidden" name="id" value="<?= $p['id'] ?>">
            <button title="Delete" class="danger"><i class="bi bi-trash"></i></button>
          </form>
        </td>
      </tr>
    <?php endforeach; if (!$rows): ?>
      <tr><td colspan="8" class="center muted">No projects found.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
  </div>
</div>
<?php require 'includes/footer.php'; ?>
