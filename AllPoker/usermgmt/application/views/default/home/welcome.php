<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="welcome-cover">
  <div class="container-fluid">
    <div class="row">
      <div class="col">
        <?php echo alert_message(); ?>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container-fluid -->
  <div class="container">
    <div class="row">
      <div class="col">
        <div class="pt-5 pb-5">
          <h1><?php echo lang('about'); ?></h1>
          <p><?php echo html_escape(db_config('site_about')); ?></p>
          <div class="row text-center">
            <div class="col-lg-3">
              <div class="feature">
                <img src="<?php assets_path('icons/bag.svg'); ?>" alt="">
                <h4>Premium Items</h4>
                <p>Pay with Stripe</p>
              </div>
              <!-- /.feature -->
            </div>
            <!-- /.col -->
            <div class="col-lg-3">
              <div class="feature">
                <img src="<?php assets_path('icons/file.svg'); ?>" alt="">
                <h4>Announcements</h4>
                <p>Speak to Users</p>
              </div>
              <!-- /.feature -->
            </div>
            <!-- /.col -->
            <div class="col-lg-3">
              <div class="feature">
                <img src="<?php assets_path('icons/mail.svg'); ?>" alt="">
                <h4>Support</h4>
                <p>Tickets & Messages</p>
              </div>
              <!-- /.feature -->
            </div>
            <!-- /.col -->
            <div class="col-lg-3">
              <div class="feature">
                <img src="<?php assets_path('icons/stats.svg'); ?>" alt="">
                <h4>Dashboard</h4>
                <p>Main Statistics</p>
              </div>
              <!-- /.feature -->
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container -->
</div>
<!-- /.welcome-cover -->

<?php if (db_config('enable_newsletter') == 1) { ?>
  <div class="bg-dark">
    <div class="container">
      <div class="row">
        <div class="col">
          <div class="pt-5 pb-5">
            <h1 class="text-white"><?php echo lang('newsletter'); ?></h1>
            <p class="text-white"><?php echo lang('newsletter_tagline'); ?></p>
            <form class="z-form mb-0" action="<?php echo env_url(); ?>home/subscribe" method="post">
              <div class="response-message"></div>
              <div class="form-row">
                <div class="col-md-9">
                  <input class="form-control mb-3 mb-md-0" type="email" name="email_address" placeholder="<?php echo lang('email_address'); ?>" required>
                </div>
                <!-- /.col -->
                <div class="col-md-3">
                  <button class="btn btn-warning btn-block" type="submit">
                    <i class="fas fa-check-circle mr-1"></i>
                    <?php echo lang('subscribe'); ?>
                  </button>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.form-row -->
            </form>
          </div>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container -->
  </div>
  <!-- /.bg-dark -->
<?php } ?>

<?php if (db_config('cu_enable_form') == 1) { ?>
  <div class="bg-primary">
    <div class="container">
      <div class="row">
        <div class="col">
          <div class="pt-5 pb-5 contact">
            <h1 class="text-white"><?php echo lang('contact_us'); ?></h1>
            <p class="text-white"><?php echo lang('contact_us_tagline'); ?></p>
            <form class="z-form mb-0" action="<?php echo env_url(); ?>home/send_message" method="post">
              <div class="response-message"></div>
              <div class="form-group">
                <input type="text" class="form-control" name="full_name" placeholder="<?php echo lang('full_name'); ?>" required>
              </div>
              <!-- /.form-group -->
              <div class="form-group">
                <input type="email" class="form-control" name="email_address" placeholder="<?php echo lang('email_address'); ?>" required>
              </div>
              <!-- /.form-group -->
              <div class="form-group">
                <textarea name="message" class="form-control" placeholder="<?php echo lang('message'); ?>" rows="14" required></textarea>
              </div>
              <!-- /.form-group -->

              <?php load_view('common/captcha_plugin', ['no_text_center' => true]); ?>

              <button class="btn btn-light btn-block" type="submit">
                <i class="fas fa-check-circle mr-1"></i>
                <?php echo lang('send_message'); ?>
              </button>
            </form>
          </div>
          <!-- /.contact -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container -->
  </div>
  <!-- /.bg-primary -->
<?php } ?>