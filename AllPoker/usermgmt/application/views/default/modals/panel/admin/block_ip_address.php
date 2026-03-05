<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<!-- Block IP Address Modal: -->
<div class="modal close-after" id="block-ip-address">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form class="z-form" action="<?php admin_action( 'tools/block_ip_address' ); ?>" method="post">
        <div class="modal-header">
          <h5 class="modal-title"><?php echo lang( 'block_ip_address' ); ?></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <!-- /.modal-header -->
        <div class="modal-body">
          <div class="response-message"></div>
          <div class="callout callout-info">
            <p><?php echo lang( 'blocked_ips_guide' ); ?></p>
          </div>
          <!-- /.callout -->
          <div class="form-group">
            <label for="ip-address"><?php echo lang( 'ip_address' ); ?> <span class="required">*</span></label>
            <input type="text" class="form-control" id="ip-address" name="ip_address" placeholder="<?php echo lang( 'ip_address_example' ); ?>" maxlength="45" required>
          </div>
          <!-- /.form-group -->
          <label for="reason"><?php echo lang( 'reason' ); ?></label>
          <textarea class="form-control" id="reason" name="reason" rows="5"></textarea>
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
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->