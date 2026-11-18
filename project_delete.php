<?php
require_once 'config.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $id = (int)($_POST['id'] ?? 0);
    if (is_admin()) $pdo->prepare("DELETE FROM projects WHERE id=?")->execute([$id]);
    else $pdo->prepare("DELETE FROM projects WHERE id=? AND student_id=?")->execute([$id, uid()]);
    flash('Project deleted.');
}
header('Location: projects.php');
exit;
