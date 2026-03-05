<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="login-box">
  <div class="login-logo">
    <span><?php echo html_escape( db_config( 'site_name' ) ); ?></span>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg"><?php echo lang( '2f_auth_tagline' ); ?></p>
      <form class="z-form" action="<?php echo env_url( 'actions/account/verify_2f_authentication' ); ?>" method="post" data-csrf="manual">
        <div class="response-message"></div>
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="code" placeholder="<?php echo lang( 'code' ); ?>" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
            <!-- /.input-group-text -->
          </div>
          <!-- /.input-group-append -->
        </div>
        <!-- /.input-group -->
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <div class="icheck-primary mt-0 mb-0">
                <input type="checkbox" id="remember" name="remember" value="1" checked>
                <label for="remember"><?php echo lang( 'do_not_ask_browser' ); ?></label>
              </div>
              <!-- /.icheck-primary -->
            </div>
            <!-- /.form-group -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block text-sm">
              <?php echo lang( 'continue' ); ?>
            </button>
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </form>

      <p class="mt-3 mb-1">
        <a href="<?php echo env_url( 'forgot_password' ); ?>">
          <?php echo lang( 'i_forgot_my_pass' ); ?>
        </a>
      </p>
      
      <?php if ( db_config( 'u_enable_registration' ) ) { ?>
        <p class="mb-0">
          <a href="<?php echo env_url( 'register' ); ?>">
            <?php echo lang( 'register_a_account' ); ?>
          </a>
        </p>
      <?php } ?>
      
    </div>
    <!-- /.login-card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->