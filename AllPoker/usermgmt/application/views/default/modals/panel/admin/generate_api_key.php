<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<!-- Generate API Key Modal: -->
<div class="modal" id="generate-api-key">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <form class="z-form" action="<?php admin_action( 'users/api_key/generate' ); ?>" method="post">
        <div class="modal-body">
          <div class="response-message"></div>
          <p><?php echo lang( 'sure_generate_api_key' ); ?></p>
        </div>
        <!-- /.modal-body -->
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary text-sm" data-dismiss="modal">
            <i class="fas fa-times-circle mr-2"></i> <?php echo lang( 'no' ); ?>
          </button>
          <button type="submit" class="btn btn-success text-sm">
            <i class="fas fa-check-circle mr-2"></i> <?php echo lang( 'yes' ); ?>
          </button>
          <input type="hidden" name="user_id" value="<?php echo html_escape( $user->id ); ?>">
        </div>
        <!-- /.modal-footer -->
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->