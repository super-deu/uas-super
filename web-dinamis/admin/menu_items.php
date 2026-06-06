<?php
require_once __DIR__ . '/../includes/functions.php';
requireLogin();
ensureSession();
$db = getDB();

const UPLOAD_DIR  = __DIR__ . '/../images/menu/';   // lokasi fisik file
const UPLOAD_PATH = 'images/menu/';                 // path relatif disimpan di DB

function flashRedirect(string $type, string $msg): void
{
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
    header('Location: menu_items.php');
    exit;
}

/**
 * Proses upload gambar. Mengembalikan path relatif (images/menu/xxx) bila sukses,
 * null bila tidak ada file diunggah, atau melempar RuntimeException bila invalid.
 */
function handleUpload(): ?string
{
    if (empty($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $f = $_FILES['image'];
    if ($f['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload gagal (kode ' . $f['error'] . ').');
    }
    if ($f['size'] > 3 * 1024 * 1024) {
        throw new RuntimeException('Ukuran gambar maksimal 3 MB.');
    }

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($f['tmp_name']);
    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Format gambar harus JPG, PNG, GIF, atau WEBP.');
    }

    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0775, true);
    }
    $filename = 'menu_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($f['tmp_name'], UPLOAD_DIR . $filename)) {
        throw new RuntimeException('Gagal menyimpan file gambar.');
    }
    return UPLOAD_PATH . $filename;
}

/** Hapus file gambar bila berada di folder upload kita. */
function deleteImageFile(?string $path): void
{
    if ($path && strpos($path, UPLOAD_PATH) === 0) {
        $abs = __DIR__ . '/../' . $path;
        if (is_file($abs)) {
            @unlink($abs);
        }
    }
}

// ---------- Proses aksi (POST) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create' || $action === 'update') {
        $name        = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price       = (float) ($_POST['price'] ?? 0);
        $categoryId  = (int) ($_POST['category_id'] ?? 0);

        if ($name === '' || $categoryId <= 0) {
            flashRedirect('danger', 'Nama dan kategori wajib diisi.');
        }

        try {
            $newImage = handleUpload();
        } catch (RuntimeException $e) {
            flashRedirect('danger', $e->getMessage());
        }

        if ($action === 'create') {
            $image = $newImage ?? 'images/f1.png'; // gambar default bila kosong
            $stmt = $db->prepare(
                'INSERT INTO menu_items (category_id, name, description, price, image)
                 VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([$categoryId, $name, $description, $price, $image]);
            flashRedirect('success', 'Menu berhasil ditambahkan.');
        } else {
            $id      = (int) ($_POST['id'] ?? 0);
            $current = getMenuItem($id);
            if (!$current) {
                flashRedirect('danger', 'Menu tidak ditemukan.');
            }
            $image = $current['image'];
            if ($newImage !== null) {
                deleteImageFile($current['image']);
                $image = $newImage;
            }
            $stmt = $db->prepare(
                'UPDATE menu_items SET category_id=?, name=?, description=?, price=?, image=? WHERE id=?'
            );
            $stmt->execute([$categoryId, $name, $description, $price, $image, $id]);
            flashRedirect('success', 'Menu berhasil diperbarui.');
        }
    }

    if ($action === 'delete') {
        $id   = (int) ($_POST['id'] ?? 0);
        $item = getMenuItem($id);
        if ($item) {
            $stmt = $db->prepare('DELETE FROM menu_items WHERE id = ?');
            $stmt->execute([$id]);
            deleteImageFile($item['image']);
        }
        flashRedirect('success', 'Menu dihapus.');
    }

    flashRedirect('danger', 'Aksi tidak dikenal.');
}

// ---------- Tampilan ----------
$pageAction = $_GET['action'] ?? 'list';
$editing    = null;
if ($pageAction === 'edit' && isset($_GET['id'])) {
    $editing = getMenuItem((int) $_GET['id']);
    if (!$editing) { $pageAction = 'list'; }
}
$categories = getCategories();

$title = 'Kelola Menu';
$nav   = 'menu';
require __DIR__ . '/partials/header.php';
?>

<?php if ($pageAction === 'create' || $pageAction === 'edit'): ?>
  <?php $isEdit = $pageAction === 'edit'; ?>
  <div class="card">
    <div class="card-body">
      <h5 class="mb-4"><?= $isEdit ? 'Edit Menu' : 'Tambah Menu' ?></h5>
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="<?= $isEdit ? 'update' : 'create' ?>">
        <?php if ($isEdit): ?>
          <input type="hidden" name="id" value="<?= (int) $editing['id'] ?>">
        <?php endif; ?>
        <div class="row">
          <div class="col-md-6 form-group">
            <label>Nama Menu</label>
            <input type="text" name="name" class="form-control" required value="<?= e($editing['name'] ?? '') ?>">
          </div>
          <div class="col-md-3 form-group">
            <label>Harga ($)</label>
            <input type="number" step="0.01" min="0" name="price" class="form-control" required value="<?= e((string)($editing['price'] ?? '')) ?>">
          </div>
          <div class="col-md-3 form-group">
            <label>Kategori</label>
            <select name="category_id" class="form-control" required>
              <option value="">- pilih -</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= (int) $cat['id'] ?>"
                  <?= (isset($editing['category_id']) && (int)$editing['category_id'] === (int)$cat['id']) ? 'selected' : '' ?>>
                  <?= e($cat['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-12 form-group">
            <label>Deskripsi</label>
            <textarea name="description" class="form-control" rows="3"><?= e($editing['description'] ?? '') ?></textarea>
          </div>
          <div class="col-md-6 form-group">
            <label>Gambar <?= $isEdit ? '(kosongkan bila tidak diganti)' : '' ?></label>
            <input type="file" name="image" class="form-control-file" accept="image/*">
            <?php if ($isEdit && !empty($editing['image'])): ?>
              <div class="mt-2"><img src="../<?= e($editing['image']) ?>" alt="" style="height:70px;border-radius:6px;"></div>
            <?php endif; ?>
          </div>
        </div>
        <button type="submit" class="btn btn-feane"><?= $isEdit ? 'Simpan Perubahan' : 'Tambah Menu' ?></button>
        <a href="menu_items.php" class="btn btn-link">Batal</a>
      </form>
    </div>
  </div>

<?php else: ?>
  <?php $items = getMenuItems(); ?>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Daftar Menu (<?= count($items) ?>)</h5>
    <a href="menu_items.php?action=create" class="btn btn-feane">+ Tambah Menu</a>
  </div>
  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
          <thead><tr><th>Gambar</th><th>Nama</th><th>Kategori</th><th>Harga</th><th class="text-right">Aksi</th></tr></thead>
          <tbody>
            <?php if (empty($items)): ?>
              <tr><td colspan="5" class="text-center text-muted">Belum ada menu.</td></tr>
            <?php else: foreach ($items as $item): ?>
              <tr>
                <td><img src="../<?= e($item['image']) ?>" alt="" style="width:54px;height:54px;object-fit:cover;border-radius:6px;"></td>
                <td><?= e($item['name']) ?></td>
                <td><?= e($item['category_name']) ?></td>
                <td>$<?= number_format((float)$item['price'], 0) ?></td>
                <td class="text-right">
                  <a href="menu_items.php?action=edit&id=<?= (int) $item['id'] ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                  <form method="post" class="d-inline" onsubmit="return confirm('Hapus menu ini?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
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
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>
