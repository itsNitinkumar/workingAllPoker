<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="login-box">
  <div class="login-logo">
    <span><?php echo html_escape(db_config('site_name')); ?></span>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg"><?php echo lang('forgot_password_tagline'); ?></p>
      <form class="z-form" action="<?php echo env_url('actions/account/request_password'); ?>" method="post" data-csrf="manual">
        <div class="response-message"><?php echo alert_message(); ?></div>
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <div class="input-group mb-3">
          <input type="email" class="form-control" name="email_address" placeholder="<?php echo lang('email_address'); ?>" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
            <!-- /.input-group-text -->
          </div>
          <!-- /.input-group-append -->
        </div>
        <!-- /.input-group -->

        <?php load_view('common/captcha_plugin'); ?>

        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block text-sm">
              <?php echo lang('request_new_pass'); ?>
            </button>
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </form>
      <p class="mt-3 mb-1">
        <a href="<?php echo env_url('login'); ?>">
          <?php echo lang('login'); ?>
        </a>
      </p>

      <?php if (db_config('u_enable_registration')) { ?>
        <p class="mb-0">
          <a href="<?php echo env_url('register'); ?>">
            <?php echo lang('register_a_account'); ?>
          </a>
        </p>
      <?php } ?>

    </div>
    <!-- /.login-card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->