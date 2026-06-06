<?php
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$db = getDB();
$totalItems      = (int) $db->query('SELECT COUNT(*) FROM menu_items')->fetchColumn();
$totalCategories = (int) $db->query('SELECT COUNT(*) FROM categories')->fetchColumn();
$latest          = $db->query(
    'SELECT m.*, c.name AS category_name FROM menu_items m
     JOIN categories c ON c.id = m.category_id
     ORDER BY m.id DESC LIMIT 5'
)->fetchAll();

$title = 'Dashboard';
$nav   = 'dashboard';
require __DIR__ . '/partials/header.php';
?>
<div class="row">
  <div class="col-md-4 mb-4">
    <div class="card">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <h6 class="text-muted mb-1">Total Menu</h6>
          <h2 class="mb-0"><?= $totalItems ?></h2>
        </div>
        <i class="fa fa-cutlery fa-2x text-warning"></i>
      </div>
    </div>
  </div>
  <div class="col-md-4 mb-4">
    <div class="card">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <h6 class="text-muted mb-1">Total Kategori</h6>
          <h2 class="mb-0"><?= $totalCategories ?></h2>
        </div>
        <i class="fa fa-tags fa-2x text-warning"></i>
      </div>
    </div>
  </div>
  <div class="col-md-4 mb-4">
    <div class="card">
      <div class="card-body">
        <h6 class="text-muted mb-2">Aksi Cepat</h6>
        <a href="menu_items.php?action=create" class="btn btn-feane btn-sm mb-1">+ Tambah Menu</a>
        <a href="categories.php" class="btn btn-outline-secondary btn-sm mb-1">Kelola Kategori</a>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <h5 class="mb-3">Menu Terbaru</h5>
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead><tr><th>Gambar</th><th>Nama</th><th>Kategori</th><th>Harga</th></tr></thead>
        <tbody>
          <?php if (empty($latest)): ?>
            <tr><td colspan="4" class="text-center text-muted">Belum ada menu.</td></tr>
          <?php else: foreach ($latest as $row): ?>
            <tr>
              <td><img src="../<?= e($row['image']) ?>" alt="" style="width:48px;height:48px;object-fit:cover;border-radius:6px;"></td>
              <td><?= e($row['name']) ?></td>
              <td><?= e($row['category_name']) ?></td>
              <td>$<?= number_format((float)$row['price'], 0) ?></td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
