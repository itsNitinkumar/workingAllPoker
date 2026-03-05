<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="login-box">
  <div class="login-logo">
    <span><?php echo html_escape(db_config('site_name')); ?></span>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg"><?php echo lang('login_tagline'); ?></p>
      <form class="z-form" action="<?php echo env_url('actions/account/login'); ?>" method="post" data-csrf="manual">
        <div class="response-message"><?php echo alert_message(); ?></div>
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="username" placeholder="<?php echo lang('username_email_address'); ?>" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
            <!-- /.input-group-text -->
          </div>
          <!-- /.input-group-append -->
        </div>
        <!-- /.input-group -->
        <div class="input-group mb-3">
          <input type="password" class="form-control" name="password" placeholder="<?php echo lang('password'); ?>" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
            <!-- /.input-group-text -->
          </div>
          <!-- /.input-group-append -->
        </div>
        <!-- /.input-group -->

        <?php load_view('common/captcha_plugin'); ?>

        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember" name="remember_me">
              <label for="remember">
                <?php echo lang('remember_me'); ?>
              </label>
            </div>
            <!-- /.icheck-primary -->
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block text-sm">
              <?php echo lang('login'); ?>
            </button>
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </form>

      <?php load_view('user/account/social_auth_links'); ?>

      <p class="mb-1">
        <a href="<?php echo env_url('forgot_password'); ?>">
          <?php echo lang('i_forgot_my_pass'); ?>
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