<?php
require_once 'config.php';
if (!empty($_SESSION['user'])) { header('Location: dashboard.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $conf = $_POST['confirm'] ?? '';
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $error = 'Enter a valid name and email.';
    elseif (strlen($pass) < 6) $error = 'Password must be at least 6 characters.';
    elseif ($pass !== $conf) $error = 'Passwords do not match.';
    else {
        $chk = $pdo->prepare("SELECT id FROM users WHERE email=?");
        $chk->execute([$email]);
        if ($chk->fetch()) $error = 'This email is already registered.';
        else {
            $pdo->prepare("INSERT INTO users(name,email,password,role) VALUES(?,?,?,'student')")
                ->execute([$name, $email, password_hash($pass, PASSWORD_DEFAULT)]);
            flash('Account created! Please sign in.');
            header('Location: index.php'); exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Register | SPMS</title>
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
      <h1>Start managing<br>your project today</h1>
      <p>Create your student account and submit your first project in under a minute.</p>
    </div>
  </div>
  <div class="auth-card">
    <button class="theme-toggle auth-theme-toggle" type="button" data-theme-toggle aria-label="Switch to dark mode" title="Switch to dark mode">
      <i class="bi bi-moon-stars-fill" aria-hidden="true"></i>
    </button>
    <h2>Create account ✨</h2>
    <p class="muted">Student registration</p>
    <?php if ($error): ?><div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill"></i> <?= e($error) ?></div><?php endif; ?>
    <form method="post" autocomplete="off">
      <label>Full name</label>
      <div class="input-icon"><i class="bi bi-person"></i><input name="name" value="<?= e($_POST['name'] ?? '') ?>" required></div>
      <label>Email</label>
      <div class="input-icon"><i class="bi bi-envelope"></i><input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required></div>
      <label>Password</label>
      <div class="input-icon"><i class="bi bi-lock"></i><input type="password" name="password" required><button class="password-toggle" type="button" aria-label="Show password" title="Show password"><i class="bi bi-eye" aria-hidden="true"></i></button></div>
      <label>Confirm password</label>
      <div class="input-icon"><i class="bi bi-shield-lock"></i><input type="password" name="confirm" required><button class="password-toggle" type="button" aria-label="Show password" title="Show password"><i class="bi bi-eye" aria-hidden="true"></i></button></div>
      <button class="btn btn-primary w-100" type="submit">Register <i class="bi bi-arrow-right"></i></button>
    </form>
    <p class="center muted">Already registered? <a href="index.php">Sign in</a></p>
  </div>
</div>
<script src="assets/app.js?v=20261030-dashboard"></script>
</body>
</html>
