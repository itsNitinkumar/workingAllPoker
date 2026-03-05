<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-12">
        <form class="z-form" action="<?php user_action( 'account/update_social_links' ); ?>" method="post" data-csrf="manual">
          <div class="response-message"><?php echo alert_message(); ?></div>
          <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <h3 class="card-title"><?php echo lang( 'social_links' ); ?></h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="form-group">
                <label for="facebook"><?php echo lang( 'facebook' ); ?></label>
                <input type="url" id="facebook" class="form-control" name="facebook" value="<?php echo html_esc_url( $this->zuser->get( 'facebook' ) ); ?>">
              </div>
              <!-- /.form-group -->
              <div class="form-group">
                <label for="twitter"><?php echo lang( 'twitter' ); ?></label>
                <input type="url" id="twitter" class="form-control" name="twitter" value="<?php echo html_esc_url( $this->zuser->get( 'twitter' ) ); ?>">
              </div>
              <!-- /.form-group -->
              <div class="form-group">
                <label for="linkedin"><?php echo lang( 'linkedin' ); ?></label>
                <input type="url" id="linkedin" class="form-control" name="linkedin" value="<?php echo html_esc_url( $this->zuser->get( 'linkedin' ) ); ?>">
              </div>
              <!-- /.form-group -->
              <label for="youtube"><?php echo lang( 'youtube' ); ?></label>
              <input type="url" id="youtube" class="form-control" name="youtube" value="<?php echo html_esc_url( $this->zuser->get( 'youtube' ) ); ?>">
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <button type="submit" class="btn btn-primary float-right text-sm">
                <i class="fas fa-check-circle mr-2"></i> <?php echo lang( 'update' ); ?>
              </button>
            </div>
            <!-- /.card-footer -->
          </div>
          <!-- /.card -->
        </form>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container-fluid -->
</div>
<!-- /.content -->