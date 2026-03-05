<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<form class="z-form" action="<?php admin_action( 'tools/send_email_template' ); ?>" method="post">
  <div class="modal-header">
    <h5 class="modal-title"><?php echo lang( 'test_email_template' ); ?></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <!-- /.modal-header -->
  <div class="modal-body">
    <div class="response-message"></div>
    <div class="callout callout-info">
      <p><?php echo lang( 'test_et_guide' ); ?></p>
    </div>
    <!-- /.callout -->
    <label for="email-address"><?php echo lang( 'email_address' ); ?> <span class="required">*</span></label>
    <input type="email" class="form-control" id="email-address" name="email_address" required>
  </div>
  <!-- /.modal-body -->
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary text-sm" data-dismiss="modal">
      <i class="fas fa-times-circle mr-2"></i> <?php echo lang( 'close' ); ?>
    </button>
    <button type="submit" class="btn btn-primary text-sm">
      <i class="fas fa-check-circle mr-2"></i> <?php echo lang( 'send' ); ?>
    </button>
  </div>
  <!-- /.modal-footer -->
  
  <input type="hidden" name="id" value="<?php echo html_escape( $id ); ?>">
</form>