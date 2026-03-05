<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="login-box">
  <div class="login-logo">
    <span><?php echo html_escape(db_config('site_name')); ?></span>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg"><?php echo lang('change_password_tagline'); ?></p>
      <form class="z-form" action="<?php echo env_url('actions/account/change_password'); ?>" method="post" data-csrf="manual">
        <div class="response-message"><?php echo alert_message(); ?></div>
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
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
        <div class="input-group mb-3">
          <input type="password" class="form-control" name="retype_password" placeholder="<?php echo lang('retype_password'); ?>" required>
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
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block text-sm">
              <?php echo lang('change_password'); ?>
            </button>
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->

        <input type="hidden" name="token" value="<?php echo html_escape($token); ?>">
      </form>
      <p class="mt-3 mb-1">
        <a href="<?php echo env_url('login'); ?>">
          <?php echo lang('login'); ?>
        </a>
      </p>
    </div>
    <!-- /.login-card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->