<?php
require_once __DIR__ . '/../includes/functions.php';
requireLogin();
ensureSession();
$db = getDB();

/** Simpan pesan flash lalu redirect. */
function flashRedirect(string $type, string $msg): void
{
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
    header('Location: categories.php');
    exit;
}

// ---------- Proses aksi (POST) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $name   = trim($_POST['name'] ?? '');

    if ($action === 'create' || $action === 'update') {
        if ($name === '') {
            flashRedirect('danger', 'Nama kategori wajib diisi.');
        }
        $slug = slugify($name);
        if ($slug === '') {
            flashRedirect('danger', 'Nama kategori tidak valid.');
        }
    }

    if ($action === 'create') {
        try {
            $stmt = $db->prepare('INSERT INTO categories (name, slug) VALUES (?, ?)');
            $stmt->execute([$name, $slug]);
            flashRedirect('success', 'Kategori berhasil ditambahkan.');
        } catch (PDOException $e) {
            flashRedirect('danger', 'Gagal: kategori mungkin sudah ada.');
        }
    }

    if ($action === 'update') {
        $id = (int) ($_POST['id'] ?? 0);
        try {
            $stmt = $db->prepare('UPDATE categories SET name = ?, slug = ? WHERE id = ?');
            $stmt->execute([$name, $slug, $id]);
            flashRedirect('success', 'Kategori berhasil diperbarui.');
        } catch (PDOException $e) {
            flashRedirect('danger', 'Gagal: nama/slug mungkin sudah dipakai.');
        }
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = $db->prepare('DELETE FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        flashRedirect('success', 'Kategori dihapus (beserta menu di dalamnya).');
    }

    flashRedirect('danger', 'Aksi tidak dikenal.');
}

// ---------- Tampilan ----------
$editing = null;
if (($_GET['action'] ?? '') === 'edit' && isset($_GET['id'])) {
    $editing = getCategory((int) $_GET['id']);
}
$categories = getCategories();

$title = 'Kelola Kategori';
$nav   = 'categories';
require __DIR__ . '/partials/header.php';
?>
<div class="row">
  <div class="col-md-4 mb-4">
    <div class="card">
      <div class="card-body">
        <h5 class="mb-3"><?= $editing ? 'Edit Kategori' : 'Tambah Kategori' ?></h5>
        <form method="post">
          <input type="hidden" name="action" value="<?= $editing ? 'update' : 'create' ?>">
          <?php if ($editing): ?>
            <input type="hidden" name="id" value="<?= (int) $editing['id'] ?>">
          <?php endif; ?>
          <div class="form-group">
            <label>Nama Kategori</label>
            <input type="text" name="name" class="form-control" required
                   value="<?= e($editing['name'] ?? '') ?>">
            <small class="text-muted">Slug dibuat otomatis (mis. "Ice Cream" &rarr; ice-cream).</small>
          </div>
          <button type="submit" class="btn btn-feane btn-block"><?= $editing ? 'Simpan Perubahan' : 'save' ?></button>
          <?php if ($editing): ?>
            <a href="categories.php" class="btn btn-link btn-block">Batal</a>
          <?php endif; ?>
        </form>
      </div>
    </div>
  </div>

  <div class="col-md-8 mb-4">
    <div class="card">
      <div class="card-body">
        <h5 class="mb-3">Daftar Kategori</h5>
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead><tr><th>#</th><th>Nama</th><th>Slug</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
              <?php if (empty($categories)): ?>
                <tr><td colspan="4" class="text-center text-muted">Belum ada kategori.</td></tr>
              <?php else: foreach ($categories as $i => $cat): ?>
                <tr>
                  <td><?= $i + 1 ?></td>
                  <td><?= e($cat['name']) ?></td>
                  <td><code><?= e($cat['slug']) ?></code></td>
                  <td class="text-right">
                    <a href="categories.php?action=edit&id=<?= (int) $cat['id'] ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                    <form method="post" class="d-inline" onsubmit="return confirm('Hapus kategori ini? Semua menu di dalamnya ikut terhapus.');">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?= (int) $cat['id'] ?>">
                      <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
