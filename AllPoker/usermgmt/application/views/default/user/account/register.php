<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="register-box">
  <div class="register-logo">
    <span><?php echo html_escape(db_config('site_name')); ?></span>
  </div>
  <!-- /.register-logo -->
  <div class="card">
    <div class="card-body register-card-body">
      <p class="login-box-msg"><?php echo lang('register_a_account'); ?></p>
      <form class="z-form" action="<?php echo env_url('actions/account/register'); ?>" method="post" data-csrf="manual">
        <div class="response-message"><?php echo alert_message(); ?></div>
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="first_name" placeholder="<?php echo lang('first_name'); ?>" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
            <!-- /.input-group-text -->
          </div>
          <!-- /.input-group-append -->
        </div>
        <!-- /.input-group -->
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="last_name" placeholder="<?php echo lang('last_name'); ?>" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
            <!-- /.input-group-text -->
          </div>
          <!-- /.input-group-append -->
        </div>
        <!-- /.input-group -->
        <div class="input-group mb-3">
          <?php if (! empty($email_address)) { ?>
            <input type="email" class="form-control" name="email_address" placeholder="<?php echo lang('email_address'); ?>" value="<?php echo html_escape($email_address); ?>" readonly>
          <?php } else { ?>
            <input type="email" class="form-control" name="email_address" placeholder="<?php echo lang('email_address'); ?>" required>
          <?php } ?>
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

        <?php load_view('common/panel/user/custom_fields'); ?>
        <?php load_view('common/captcha_plugin'); ?>

        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="agreeTerms" name="terms" value="agree" required>
              <label for="agreeTerms">
                <?php printf(lang('agree_terms'), env_url('terms')); ?>
              </label>
            </div>
            <!-- /.icheck-primary -->
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block text-sm">
              <?php echo lang('register'); ?>
            </button>
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
        <?php if (! empty($code)) { ?>
          <input type="hidden" name="invitation_code" value="<?php echo html_escape($code); ?>">
        <?php } ?>
      </form>

      <?php load_view('user/account/social_auth_links'); ?>

      <a href="<?php echo env_url('login'); ?>">
        <?php echo lang('already_registered'); ?>
      </a>
    </div>
    <!-- /.form-box -->
  </div>
  <!-- /.card -->
</div>
<!-- /.register-box -->