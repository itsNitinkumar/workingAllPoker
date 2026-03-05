<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<form class="z-form" action="<?php admin_action( 'payment/update_item' ); ?>" method="post">
  <div class="modal-header">
    <h5 class="modal-title"><?php echo lang( 'edit_item' ); ?></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <!-- /.modal-header -->
  <div class="modal-body">
    <div class="response-message"></div>
    <div class="form-row">
      <div class="form-group col-md-6">
        <label for="name-edit"><?php echo lang( 'name' ); ?> <span class="required">*</span></label>
        <input type="text" class="form-control" id="name-edit" name="name" value="<?php echo html_escape( $name ); ?>" required>
      </div>
      <!-- /.form-group -->
      <div class="form-group col-md-6">
        <label for="type-edit"><?php echo lang( 'type' ); ?></label>
        <select class="form-control select2 search-disabled" id="type-edit" name="type">
          <option value="purchase" <?php echo select_single( 'purchase', $type ); ?>><?php echo lang( 'purchase' ); ?></option>
          <option value="top_up" <?php echo select_single( 'top_up', $type ); ?>><?php echo lang( 'top_up' ); ?></option>
        </select>
      </div>
      <!-- /.form-group -->
    </div>
    <!-- /.form-row -->
    <div class="form-group">
      <label for="currency-edit"><?php echo lang( 'currency' ); ?></label>
      <select class="form-control select2" id="currency-edit" name="currency">
        <?php foreach ( get_currencies() as $currency ) { ?>
          <option value="<?php echo html_escape( $currency->id ); ?>" <?php echo select_single( $currency->id, $currency_id ); ?>><?php echo html_escape( $currency->code . ' - ' . $currency->name ); ?></option>
        <?php } ?>
      </select>
    </div>
    <!-- /.form-group -->
    <div class="form-row">
      <div class="form-group col-md-6">
        <label for="price-edit"><?php echo lang( 'price' ); ?> <span class="required">*</span></label>
        <input type="number" step="0.01" class="form-control" id="price-edit" name="price" value="<?php echo html_escape( $price ); ?>" required>
      </div>
      <!-- /.form-group -->
      <div class="form-group col-md-6">
        <label for="days-edit">
          <?php echo lang( 'days' ); ?>
          <i class="fas fa-info-circle text-sm" data-toggle="tooltip" title="<?php echo lang( 'item_days_tip' ); ?>"></i>
        </label>
        <input type="number" class="form-control" id="days-edit" name="days" value="<?php echo html_escape( $days ); ?>">
      </div>
      <!-- /.form-group -->
    </div>
    <!-- /.form-row -->
    <div class="form-group">
      <label for="description-edit"><?php echo lang( 'description' ); ?> <span class="required">*</span></label>
      <textarea class="form-control" id="description-edit" name="description" rows="5" required><?php echo html_escape( $description ); ?></textarea>
    </div>
    <!-- /.form-group -->
    <label for="status-edit"><?php echo lang( 'status' ); ?></label>
    <select class="form-control select2 search-disabled" id="status-edit" name="status">
      <option value="1" <?php echo select_single( 1, $status ); ?>><?php echo lang( 'active' ); ?></option>
      <option value="0" <?php echo select_single( 0, $status ); ?>><?php echo lang( 'deactive' ); ?></option>
    </select>
  </div>
  <!-- /.modal-body -->
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary text-sm" data-dismiss="modal">
      <i class="fas fa-times-circle mr-2"></i> <?php echo lang( 'close' ); ?>
    </button>
    <button type="submit" class="btn btn-primary text-sm">
      <i class="fas fa-check-circle mr-2"></i> <?php echo lang( 'update' ); ?>
    </button>
  </div>
  <!-- /.modal-footer -->
  
  <input type="hidden" name="id" value="<?php echo html_escape( $id ); ?>">
</form>