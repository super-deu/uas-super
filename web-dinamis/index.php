<?php
require_once __DIR__ . '/includes/functions.php';
$categories = getCategories();
$items      = getMenuItems();
$active = 'home';
$show_slider = true;
require __DIR__ . '/includes/header.php';
?>

  <!-- offer section -->
  <section class="offer_section layout_padding-bottom">
    <div class="offer_container">
      <div class="container ">
        <div class="row">
          <div class="col-md-6  ">
            <div class="box ">
              <div class="img-box">
                <img src="images/o1.jpg" alt="">
              </div>
              <div class="detail-box">
                <h5>Tasty Thursdays</h5>
                <h6><span>20%</span> Off</h6>
                <a href="menu.php">Order Now</a>
              </div>
            </div>
          </div>
          <div class="col-md-6  ">
            <div class="box ">
              <div class="img-box">
                <img src="images/o2.jpg" alt="">
              </div>
              <div class="detail-box">
                <h5>Pizza Days</h5>
                <h6><span>15%</span> Off</h6>
                <a href="menu.php">Order Now</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- end offer section -->

  <!-- food section -->
  <section class="food_section layout_padding-bottom">
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
            <div class="col-12 text-center"><p>Belum ada menu.</p></div>
          <?php else: ?>
            <?php foreach ($items as $item): ?>
              <?php require __DIR__ . '/includes/menu_card.php'; ?>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
      <div class="btn-box">
        <a href="menu.php">View More</a>
      </div>
    </div>
  </section>
  <!-- end food section -->

  <!-- about section -->
  <section class="about_section layout_padding">
    <div class="container  ">
      <div class="row">
        <div class="col-md-6 ">
          <div class="img-box">
            <img src="images/about-img.png" alt="">
          </div>
        </div>
        <div class="col-md-6">
          <div class="detail-box">
            <div class="heading_container">
              <h2>We Are Feane</h2>
            </div>
            <p>
              There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration
              in some form, by injected humour, or randomised words which don't look even slightly believable. If you
              are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in
              the middle of text. All
            </p>
            <a href="about.php">Read More</a>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- end about section -->

  <!-- book section -->
  <section class="book_section layout_padding">
    <div class="container">
      <div class="heading_container">
        <h2>Book A Table</h2>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form_container">
            <form action="book.php" method="post">
              <div><input type="text" name="name" class="form-control" placeholder="Your Name" /></div>
              <div><input type="text" name="phone" class="form-control" placeholder="Phone Number" /></div>
              <div><input type="email" name="email" class="form-control" placeholder="Your Email" /></div>
              <div>
                <select class="form-control nice-select wide" name="persons">
                  <option value="" disabled selected>How many persons?</option>
                  <option>2</option><option>3</option><option>4</option><option>5</option>
                </select>
              </div>
              <div><input type="date" name="date" class="form-control"></div>
              <div class="btn_box">
                <button type="submit">Book Now</button>
              </div>
            </form>
          </div>
        </div>
        <div class="col-md-6">
          <div class="map_container ">
            <div id="googleMap"></div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- end book section -->

  <!-- client section -->
  <section class="client_section layout_padding-bottom">
    <div class="container">
      <div class="heading_container heading_center psudo_white_primary mb_45">
        <h2>What Says Our Customers</h2>
      </div>
      <div class="carousel-wrap row ">
        <div class="owl-carousel client_owl-carousel">
          <div class="item">
            <div class="box">
              <div class="detail-box">
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam</p>
                <h6>Moana Michell</h6>
                <p>magna aliqua</p>
              </div>
              <div class="img-box">
                <img src="images/client1.jpg" alt="" class="box-img">
              </div>
            </div>
          </div>
          <div class="item">
            <div class="box">
              <div class="detail-box">
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam</p>
                <h6>Mike Hamell</h6>
                <p>magna aliqua</p>
              </div>
              <div class="img-box">
                <img src="images/client2.jpg" alt="" class="box-img">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- end client section -->

<?php require __DIR__ . '/includes/footer.php'; ?>
