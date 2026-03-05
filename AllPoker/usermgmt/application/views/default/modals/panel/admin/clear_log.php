<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<?php if ( ! empty( $activities ) ) { ?>
  <!-- Clear Log Modal: -->
  <div class="modal" id="clear-log">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form class="z-form" action="<?php admin_action( 'tools/clear_log' ); ?>" method="post">
          <div class="modal-body">
            <div class="response-message"></div>
            <p class="mb-2"><?php echo lang( 'sure_clear_log' ); ?></p>
            <select class="form-control select2 search-disabled" name="period">
              <option value="1"><?php echo lang( 'clear_older_3' ); ?></option>
              <option value="2"><?php echo lang( 'clear_older_7' ); ?></option>
              <option value="3"><?php echo lang( 'clear_older_14' ); ?></option>
              <option value="4"><?php echo lang( 'clear_older_1m' ); ?></option>
              <option value="5"><?php echo lang( 'clear_older_3m' ); ?></option>
              <option value="6"><?php echo lang( 'clear_older_6m' ); ?></option>
              <option value="7"><?php echo lang( 'clear_older_1y' ); ?></option>
              <option value="8"><?php echo lang( 'clear_all' ); ?></option>
            </select>
          </div>
          <!-- /.modal-body -->
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary text-sm" data-dismiss="modal">
              <i class="fas fa-times-circle mr-2"></i> <?php echo lang( 'no' ); ?>
            </button>
            <button type="submit" class="btn btn-danger text-sm">
              <i class="fas fa-check-circle mr-2"></i> <?php echo lang( 'yes' ); ?>
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
<?php } ?>