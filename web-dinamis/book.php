<?php
require_once __DIR__ . '/includes/functions.php';
$active = 'book';
$show_slider = false;
require __DIR__ . '/includes/header.php';
?>

  <!-- book section -->
  <section class="book_section layout_padding">
    <div class="container">
      <div class="heading_container">
        <h2>Book A Table</h2>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form_container">
            <form action="" method="post">
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

<?php require __DIR__ . '/includes/footer.php'; ?>
