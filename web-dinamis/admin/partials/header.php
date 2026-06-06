<?php
/**
 * Layout admin (sidebar + topbar). Expects:
 *   $title  string  judul halaman
 *   $nav    string  'dashboard' | 'categories' | 'menu'
 */
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();
$title = $title ?? 'Admin';
$nav   = $nav ?? '';
function aActive(string $n, string $nav): string { return $n === $nav ? ' active' : ''; }

ensureSession();
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Feane Admin &middot; <?= e($title) ?></title>
  <link rel="stylesheet" href="../css/bootstrap.css">
  <link href="../css/font-awesome.min.css" rel="stylesheet">
  <style>
    :root { --feane:#ffbe33; --dark:#1e1e2d; }
    body { background:#f4f5f9; }
    .admin-sidebar {
      position:fixed; top:0; left:0; bottom:0; width:240px; background:var(--dark);
      color:#cfd0e0; padding:0; overflow-y:auto;
    }
    .admin-sidebar .brand { color:#fff; font-size:1.5rem; font-weight:700; padding:20px 24px; display:block; }
    .admin-sidebar .brand span { color:var(--feane); }
    .admin-sidebar a.nav-link { color:#cfd0e0; padding:12px 24px; display:flex; align-items:center; gap:10px; }
    .admin-sidebar a.nav-link:hover, .admin-sidebar a.nav-link.active { background:rgba(255,190,51,.15); color:#fff; border-left:3px solid var(--feane); }
    .admin-main { margin-left:240px; }
    .admin-topbar { background:#fff; padding:14px 28px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 1px 4px rgba(0,0,0,.06); }
    .admin-content { padding:28px; }
    .btn-feane { background:var(--feane); color:#fff; border:none; }
    .btn-feane:hover { background:#e9ab1f; color:#fff; }
    .card { border:none; box-shadow:0 1px 6px rgba(0,0,0,.06); }
    .table thead th { border-top:none; }
    @media (max-width:768px){ .admin-sidebar{position:static;width:100%;} .admin-main{margin-left:0;} }
  </style>
</head>
<body>
  <aside class="admin-sidebar">
    <a href="dashboard.php" class="brand">Fea<span>ne</span> Admin</a>
    <nav class="nav flex-column">
      <a class="nav-link<?= aActive('dashboard',$nav) ?>" href="dashboard.php"><i class="fa fa-tachometer"></i> Dashboard</a>
      <a class="nav-link<?= aActive('categories',$nav) ?>" href="categories.php"><i class="fa fa-tags"></i> Kategori</a>
      <a class="nav-link<?= aActive('menu',$nav) ?>" href="menu_items.php"><i class="fa fa-cutlery"></i> Menu</a>
      <a class="nav-link" href="../index.php" target="_blank"><i class="fa fa-globe"></i> Lihat Website</a>
      <a class="nav-link" href="logout.php"><i class="fa fa-sign-out"></i> Logout</a>
    </nav>
  </aside>
  <div class="admin-main">
    <div class="admin-topbar">
      <h5 class="mb-0"><?= e($title) ?></h5>
      <div>Hi, <strong><?= e(currentAdminName()) ?></strong></div>
    </div>
    <div class="admin-content">
      <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show">
          <?= e($flash['msg']) ?>
          <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
      <?php endif; ?>
