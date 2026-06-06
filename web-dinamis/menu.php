<?php
require_once __DIR__ . '/includes/functions.php';
$categories = getCategories();
$items      = getMenuItems();
$active = 'menu';
$show_slider = false;
require __DIR__ . '/includes/header.php';
?>

  <!-- food section -->
  <section class="food_section layout_padding">
    <div class="container">
      <div class="heading_container heading_center">
        <h2>Our Menu</h2>
      </div>

      <ul class="filters_menu">
        <li class="active" data-filter="*">All</li>
        <?php foreach ($categories as $cat): ?>
          <li data-filter=".<?= e($cat['slug']) ?>"><?= e($cat['name']) ?></li>
        <?php endforeach; ?>
      </ul>

      <div class="filters-content">
        <div class="row grid">
          <?php if (empty($items)): ?>
            <div class="col-12 text-center">
              <p>Belum ada menu. Tambahkan lewat halaman admin.</p>
            </div>
          <?php else: ?>
            <?php foreach ($items as $item): ?>
              <?php require __DIR__ . '/includes/menu_card.php'; ?>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>
  <!-- end food section -->

<?php require __DIR__ . '/includes/footer.php'; ?>
