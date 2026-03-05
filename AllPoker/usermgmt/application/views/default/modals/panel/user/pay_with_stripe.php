<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<?php if ( is_stripe_togo() ) { ?>
  <!-- Pay with Stripe Modal: -->
  <div class="modal" id="pay-with-stripe" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form id="stripe-pay-form" action="<?php user_action( 'payment/proceed' ); ?>" method="post">
          <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
          <div class="modal-header">
            <h5 class="modal-title font-weight-bold text-primary">
              <span id="sp-main-title"></span>
              <small id="sp-sub-title" class="d-block text-muted"></small>
            </h5>
          </div>
          <!-- /.modal-header -->
          <div class="modal-body">
            <div class="card-errors alert alert-danger d-none" role="alert"></div>
            <label><?php echo lang( 'card_details' ); ?> <span class="required">*</span></label>
            <div id="card-element" class="sp-card-input"></div>
          </div>
          <!-- /.modal-body -->
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary text-sm" data-dismiss="modal">
              <i class="fas fa-times-circle mr-2"></i> <?php echo lang( 'cancel' ); ?>
            </button>
            <button type="submit" class="btn btn-primary text-sm" id="proceed">
              <i class="fas fa-check-circle mr-2"></i> <?php echo lang( 'proceed' ); ?>
            </button>
            <input type="hidden" name="stripe_token">
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