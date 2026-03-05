<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<!-- Add Payment Item Modal: -->
<div class="modal close-after" id="add-item">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form class="z-form" action="<?php admin_action( 'payment/add_item' ); ?>" method="post">
        <div class="modal-header">
          <h5 class="modal-title"><?php echo lang( 'add_item' ); ?></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <!-- /.modal-header -->
        <div class="modal-body">
          <div class="response-message"></div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="name-add"><?php echo lang( 'name' ); ?> <span class="required">*</span></label>
              <input type="text" class="form-control" id="name-add" name="name" required>
            </div>
            <!-- /.form-group -->
            <div class="form-group col-md-6">
              <label for="type-add"><?php echo lang( 'type' ); ?></label>
              <select class="form-control select2 search-disabled" id="type-add" name="type">
                <option value="purchase"><?php echo lang( 'purchase' ); ?></option>
                <option value="top_up"><?php echo lang( 'top_up' ); ?></option>
              </select>
            </div>
            <!-- /.form-group -->
          </div>
          <!-- /.form-row -->
          <div class="form-group">
            <label for="currency-add"><?php echo lang( 'currency' ); ?></label>
            <select class="form-control select2" id="currency-add" name="currency">
              <?php foreach ( get_currencies() as $currency ) { ?>
                <option value="<?php echo html_escape( $currency->id ); ?>"><?php echo html_escape( $currency->code . ' - ' . $currency->name ); ?></option>
              <?php } ?>
            </select>
          </div>
          <!-- /.form-group -->
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="price-add"><?php echo lang( 'price' ); ?> <span class="required">*</span></label>
              <input type="number" step="0.01" class="form-control" id="price-add" name="price" required>
            </div>
            <!-- /.form-group -->
            <div class="form-group col-md-6">
              <label for="days-add">
                <?php echo lang( 'days' ); ?>
                <i class="fas fa-info-circle text-sm" data-toggle="tooltip" title="<?php echo lang( 'item_days_tip' ); ?>"></i>
              </label>
              <input type="number" class="form-control" id="days-add" name="days">
            </div>
            <!-- /.form-group -->
          </div>
          <!-- /.form-row -->
          <div class="form-group">
            <label for="description-add"><?php echo lang( 'description' ); ?> <span class="required">*</span></label>
            <textarea class="form-control" id="description-add" name="description" rows="5" required></textarea>
          </div>
          <!-- /.form-group -->
          <label for="status-add"><?php echo lang( 'status' ); ?></label>
          <select class="form-control select2 search-disabled" id="status-add" name="status">
            <option value="1"><?php echo lang( 'active' ); ?></option>
            <option value="0"><?php echo lang( 'deactive' ); ?></option>
          </select>
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