<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<form class="z-form" action="<?php admin_action( 'support/reply_contact_message' ); ?>" method="post">
  <div class="modal-header">
    <h5 class="modal-title"><?php echo lang( 'contact_message_reply' ); ?></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <!-- /.modal-header -->
  <div class="modal-body">
    <div class="response-message"></div>
    <div class="form-group">
      <label for="reply-to"><?php echo lang( 'reply_to' ); ?> <span class="required">*</span></label>
      <input type="text" class="form-control" id="reply-to" value="<?php echo html_escape( $email_address ); ?>" readonly>
    </div>
    <!-- /.form-group -->
    <label for="reply-message"><?php echo lang( 'message' ); ?> <span class="required">*</span></label>
    <textarea class="form-control" id="reply-message" name="reply_message" rows="5" required></textarea>
  </div>
  <!-- /.modal-body -->
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary text-sm" data-dismiss="modal">
      <i class="fas fa-times-circle mr-2"></i> <?php echo lang( 'close' ); ?>
    </button>
    <button type="submit" class="btn btn-primary text-sm">
      <i class="fas fa-check-circle mr-2"></i> <?php echo lang( 'submit' ); ?>
    </button>
  </div>
  <!-- /.modal-footer -->
  
  <input type="hidden" name="id" value="<?php echo html_escape( $id ); ?>">
</form>