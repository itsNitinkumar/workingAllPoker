<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<?php if ( db_config( 'credit_pay_enable' ) ) { ?>
  <!-- Pay with Credit Modal: -->
  <div class="modal" id="pay-with-credit">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form class="z-form" action="<?php user_action( 'payment/proceed/credit' ); ?>" method="post">
          <div class="modal-header">
            <h5 class="modal-title font-weight-bold text-primary">
              <span id="item-main-title"></span>
              <small id="item-sub-title" class="d-block text-muted"></small>
            </h5>
          </div>
          <!-- /.modal-header -->
          <div class="modal-body">
            <div class="response-message"></div>
            <?php echo lang( 'sure_pay_with_credit' ); ?>
          </div>
          <!-- /.modal-body -->
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary text-sm" data-dismiss="modal">
              <i class="fas fa-times-circle mr-2"></i> <?php echo lang( 'cancel' ); ?>
            </button>
            <button type="submit" class="btn btn-primary text-sm">
              <i class="fas fa-check-circle mr-2"></i> <?php echo lang( 'yes' ); ?>
            </button>
            <input type="hidden" name="price">
            <input type="hidden" name="id">
          </div>
          <!-- /.modal-footer -->
        </form>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- /.modal -->
<?php } ?>