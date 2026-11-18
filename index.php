<?php
require_once 'config.php';
if (!empty($_SESSION['user'])) { header('Location: dashboard.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $st = $pdo->prepare("SELECT * FROM users WHERE email=?");
    $st->execute([$email]);
    $u = $st->fetch();
    if ($u && password_verify($pass, $u['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => $u['id'], 'name' => $u['name'], 'email' => $u['email'], 'role' => $u['role']];
        header('Location: dashboard.php'); exit;
    }
    $error = 'Invalid email or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login | Student Project Management System</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="assets/style.css?v=20261030-dashboard">
</head>
<body class="auth-body">
<div class="auth-wrap">
  <div class="auth-hero">
    <div class="orb o1"></div><div class="orb o2"></div>
    <div class="hero-content">
      <div class="logo"><i class="bi bi-mortarboard-fill"></i> SPMS</div>
      <h1>Student Project<br>Management System</h1>
      <p>Plan, track and evaluate academic projects in one beautiful place.</p>
      <ul>
        <li><i class="bi bi-check-circle-fill"></i> Real-time progress tracking</li>
        <li><i class="bi bi-check-circle-fill"></i> Guide feedback &amp; grading</li>
        <li><i class="bi bi-check-circle-fill"></i> Smart dashboard &amp; analytics</li>
      </ul>
    </div>
  </div>
  <div class="auth-card">
    <button class="theme-toggle auth-theme-toggle" type="button" data-theme-toggle aria-label="Switch to dark mode" title="Switch to dark mode">
      <i class="bi bi-moon-stars-fill" aria-hidden="true"></i>
    </button>
    <h2>Welcome back 👋</h2>
    <p class="muted">Sign in to continue to your dashboard</p>
    <?php if (!empty($_SESSION['flash'])): [$fm] = $_SESSION['flash']; unset($_SESSION['flash']); ?><div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> <?= e($fm) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill"></i> <?= e($error) ?></div><?php endif; ?>
    <form method="post" autocomplete="off">
      <label>Email</label>
      <div class="input-icon"><i class="bi bi-envelope"></i><input type="email" name="email" placeholder="you@college.edu" required></div>
      <label>Password</label>
      <div class="input-icon"><i class="bi bi-lock"></i><input type="password" name="password" placeholder="••••••••" required><button class="password-toggle" type="button" aria-label="Show password" title="Show password"><i class="bi bi-eye" aria-hidden="true"></i></button></div>
      <button class="btn btn-primary w-100" type="submit">Sign In <i class="bi bi-arrow-right"></i></button>
    </form>
    <p class="center muted">New student? <a href="register.php">Create an account</a></p>
    <div class="demo"><i class="bi bi-info-circle"></i> Demo admin: <b>admin@spms.com</b> / <b>admin123</b></div>
  </div>
</div>
<script src="assets/app.js?v=20261030-dashboard"></script>
</body>
</html>
