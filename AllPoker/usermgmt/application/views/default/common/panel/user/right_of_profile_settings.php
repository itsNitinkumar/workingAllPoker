<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<?php if ( db_config( 'u_2f_authentication' ) ) { ?>
  <div class="card">
    <div class="card-header d-flex align-items-center">
      <h3 class="card-title"><?php echo lang( '2f_authentication' ); ?></h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
      <div class="icheck icheck-primary d-inline-block mr-2">
        <input type="radio" name="two_factor_authentication" id="2fa-activation-1" value="1" <?php echo check_single( 1, $user->two_factor_authentication ); ?>>
        <label for="2fa-activation-1"><?php echo lang( 'enable' ); ?></label>
      </div>
      <!-- /.icheck -->
      <div class="icheck icheck-primary d-inline-block">
        <input type="radio" name="two_factor_authentication" id="2fa-activation-0" value="0" <?php echo check_single( 0, $user->two_factor_authentication ); ?>>
        <label for="2fa-activation-0"><?php echo lang( 'disable' ); ?></label>
      </div>
      <!-- /.icheck -->
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
<?php } ?>

<div class="card">
  <div class="card-header d-flex align-items-center">
    <h3 class="card-title"><?php echo lang( 'profile_picture' ); ?></h3>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <div id="image-preview">
      <label for="image-upload" id="image-label"><?php echo lang( 'choose_file' ); ?></label>
      <input type="file" name="picture" id="image-upload" accept="<?php echo ALLOWED_IMG_EXT_HTML; ?>">
    </div>
    <!-- /#image-preview -->
    <hr>
    <small class="form-text text-muted"><?php echo avator_tip(); ?></small>
  </div>
  <!-- /.card-body -->
  <?php if ( $user->picture !== DEFAULT_USER_IMG ) { ?>
    <div class="card-footer">
      <span class="text-primary cursor-pointer text-sm" data-toggle="modal" data-target="#view-pp">
        <i class="fas fa-image mr-1"></i> <?php echo lang( 'view' ); ?>
      </span>      
      <span class="text-danger cursor-pointer text-sm float-right" data-toggle="modal" data-target="#delete-pp">
        <i class="fas fa-trash mr-1"></i> <?php echo lang( 'delete' ); ?>
      </span>
    </div>
    <!-- /.card-footer -->
  <?php } ?>
</div>
<!-- /.card -->

<?php load_view( 'common/panel/user/user_credits' ); ?>