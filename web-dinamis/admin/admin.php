<?php
require_once __DIR__ . '/../includes/functions.php';
ensureSession();

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = getDB()->prepare('SELECT * FROM admins WHERE username = ?');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id']       = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Username atau password salah.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Admin &middot; Feane</title>
  <link rel="stylesheet" href="../css/bootstrap.css">
  <style>
    body { background:#1e1e2d; display:flex; min-height:100vh; align-items:center; }
    .login-card { max-width:380px; width:100%; margin:auto; border:none; border-radius:12px; }
    .login-card .brand { font-size:2rem; font-weight:700; text-align:center; color:#1e1e2d; }
    .login-card .brand span { color:#ffbe33; }
    .btn-feane { background:#ffbe33; color:#fff; border:none; }
    .btn-feane:hover { background:#e9ab1f; color:#fff; }
  </style>
</head>
<body>
  <div class="card login-card p-4 shadow">
    <div class="brand mb-1">Fea<span>ne</span></div>
    <p class="text-center text-muted mb-4">Panel Administrator</p>

    <?php if ($error): ?>
      <div class="alert alert-danger py-2"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" class="form-control" required autofocus>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-feane btn-block">Masuk</button>
    </form>
    <p class="text-center text-muted mt-3 mb-0"><small>Default: admin / admin123</small></p>
  </div>
</body>
</html>
