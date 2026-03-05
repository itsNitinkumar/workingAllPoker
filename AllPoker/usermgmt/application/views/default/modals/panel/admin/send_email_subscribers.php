<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<!-- Send Email to Subscribers Modal: -->
<div class="modal close-after" id="send-email-subscribers">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form class="z-form" action="<?php admin_action( 'subscribers/send_email' ); ?>" method="post">
        <div class="modal-header">
          <h5 class="modal-title"><?php echo lang( 'send_email' ); ?> <small class="text-muted">( <?php echo lang( 'only_confirmed_sub' ); ?> )</small></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <!-- /.modal-header -->
        <div class="modal-body">
          <div class="response-message"></div>
          <div class="callout callout-warning">
            <p><?php echo lang( 'newsletter_send_guide' ); ?></p>
          </div>
          <!-- /.callout -->
          <div class="form-group">
            <label for="subject"><?php echo lang( 'subject' ); ?> <span class="required">*</span></label>
            <input type="text" id="subject" class="form-control" name="subject" required>
          </div>
          <!-- /.form-group -->
          <div class="form-group">
            <label for="textarea"><?php echo lang( 'message' ); ?> <span class="required">*</span></label>
            <textarea class="form-control textarea" id="textarea" rows="6" name="message"></textarea>
          </div>
          <!-- /.form-group -->
          <label for="language">
            <?php echo lang( 'language' ); ?>
            <i class="fas fa-info-circle text-sm" data-toggle="tooltip" title="<?php echo lang( 'nl_template_lang_tip' ); ?>"></i>
          </label>
          <select class="form-control select2 search-disabled" id="language" name="language">
            <?php foreach ( AVAILABLE_LANGUAGES as $key => $value ) { ?>
              <option value="<?php echo html_escape( $key ); ?>"><?php echo html_escape( $value['display_label'] ); ?></option>
            <?php } ?>
          </select>
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
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->