<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-12">
        <form class="z-form" action="<?php admin_action( 'settings/payment' ); ?>" method="post" data-csrf="manual">
          <div class="response-message"><?php echo alert_message(); ?></div>
          <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <h3 class="card-title"><?php echo lang( 'payment_settings' ); ?></h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="form-group">
                <label for="enable-credit-pay"><?php echo lang( 'enable_credit_pay' ); ?></label>
                <select id="enable-credit-pay" class="form-control select2 search-disabled" name="credit_pay_enable">
                  <option value="1" <?php echo select_single( 1, db_config( 'credit_pay_enable' ) ); ?>><?php echo lang( 'yes' ); ?></option>
                  <option value="0" <?php echo select_single( 0, db_config( 'credit_pay_enable' ) ); ?>><?php echo lang( 'no' ); ?></option>
                </select>
              </div>
              <!-- /.form-group -->
              <div class="form-group">
                <label for="phone-number"><?php echo lang( 'phone_number' ); ?></label>
                <input type="text" id="phone-number" class="form-control" name="iv_phone_number" value="<?php echo html_escape( db_config( 'iv_phone_number' ) ); ?>">
              </div>
              <!-- /.form-group -->
              <div class="form-group">
                <label for="company-name"><?php echo lang( 'issued_company_name' ); ?></label>
                <input type="text" id="company-name" class="form-control" name="iv_company_name" value="<?php echo html_escape( db_config( 'iv_company_name' ) ); ?>">
              </div>
              <!-- /.form-group -->
              <div class="form-group">
                <label for="address-line-1"><?php echo lang( 'address_line_1' ); ?></label>
                <input type="text" id="address-line-1" class="form-control" name="iv_address_1" value="<?php echo html_escape( db_config( 'iv_address_1' ) ); ?>">
              </div>
              <!-- /.form-group -->
              <label for="address-line-2"><?php echo lang( 'address_line_2' ); ?></label>
              <input type="text" id="address-line-2" class="form-control" name="iv_address_2" value="<?php echo html_escape( db_config( 'iv_address_2' ) ); ?>">
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <button type="submit" class="btn btn-primary float-right text-sm">
                <i class="fas fa-check-circle mr-2"></i> <?php echo lang( 'update' ); ?>
              </button>
            </div>
            <!-- /.card-footer -->
          </div>
          <!-- /.card -->
        </form>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container-fluid -->
</div>
<!-- /.content -->