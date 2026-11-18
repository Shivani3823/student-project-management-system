<?php
session_start();

$DB_HOST = 'localhost';
$DB_NAME = 'spms';
$DB_USER = 'root';
$DB_PASS = '';

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $ex) {
    die("<h3 style='font-family:sans-serif'>Database error: import <b>database.sql</b> in phpMyAdmin first.<br><small>" . htmlspecialchars($ex->getMessage()) . "</small></h3>");
}

// Auto-create default admin on first run
if ($pdo->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn() == 0) {
    $pdo->prepare("INSERT INTO users(name,email,password,role) VALUES(?,?,?,'admin')")
        ->execute(['Project Guide', 'admin@spms.com', password_hash('admin123', PASSWORD_DEFAULT)]);
}

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function require_login() {
    if (empty($_SESSION['user'])) { header('Location: index.php'); exit; }
}
function is_admin() { return ($_SESSION['user']['role'] ?? '') === 'admin'; }
function uid() { return (int)($_SESSION['user']['id'] ?? 0); }
function flash($msg, $type = 'success') { $_SESSION['flash'] = [$msg, $type]; }
function csrf() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return $_SESSION['csrf'];
}
function check_csrf() {
    if (($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? 'x')) { die('Invalid request token.'); }
}
function badge($status) {
    $c = ['Pending' => 'b-pending', 'In Progress' => 'b-progress', 'Completed' => 'b-done'][$status] ?? 'b-pending';
    return "<span class='badge $c'>" . e($status) . "</span>";
}
