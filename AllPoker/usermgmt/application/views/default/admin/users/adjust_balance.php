<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="content">
  <div class="container-fluid">
    <form class="z-form" action="<?php admin_action( 'users/adjust_balance' ); ?>" method="post" data-csrf="manual">
      <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
      <div class="row">
        <div class="col">
          <div class="response-message"><?php echo alert_message(); ?></div>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
      <div class="row">
        <div class="col-md-9">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <h3 class="card-title"><?php echo lang( 'adjust_balance' ); ?></h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="email-address"><?php echo lang( 'email_address' ); ?></label>
                  <input type="email" id="email-address" class="form-control" value="<?php echo html_escape( $email_address ); ?>" readonly>
                </div>
                <!-- /.form-group -->
                <div class="form-group col-md-6">
                  <label for="type"><?php echo lang( 'type' ); ?></label>
                  <select id="type" class="form-control select2 search-disabled" name="type">
                    <option value="add_credit"><?php echo lang( 'add_credit' ); ?></option>
                    <option value="cut_credit"><?php echo lang( 'cut_credit' ); ?></option>
                  </select>
                </div>
                <!-- /.form-group -->
              </div>
              <!-- /.form-row -->
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="currency"><?php echo lang( 'currency' ); ?></label>
                  <select class="form-control select2" id="currency" name="currency">
                    <?php foreach ( get_currencies() as $currency ) { ?>
                      <option value="<?php echo html_escape( $currency->id ); ?>"><?php echo html_escape( $currency->code . ' - ' . $currency->name ); ?></option>
                    <?php } ?>
                  </select>
                </div>
                <!-- /.form-group -->
                <div class="form-group col-md-6">
                  <label for="payment-gateway"><?php echo lang( 'payment_gateway' ); ?></label>
                  <select id="payment-gateway" class="form-control select2 search-disabled" name="payment_gateway">
                    <option value="bank_transfer"><?php echo lang( 'bank_transfer' ); ?></option>
                    <option value="cash"><?php echo lang( 'cash' ); ?></option>
                    <option value="others"><?php echo lang( 'others' ); ?></option>
                  </select>
                </div>
                <!-- /.form-group -->
              </div>
              <!-- /.form-row -->
              <div class="form-row">
                <div class="col-md-6">
                  <label for="log-visible-to-user"><?php echo lang( 'log_visible_to_user' ); ?></label>
                  <select id="log-visible-to-user" class="form-control select2 search-disabled" name="visible_to_user">
                    <option value="0"><?php echo lang( 'no' ); ?></option>
                    <option value="1"><?php echo lang( 'yes' ); ?></option>
                  </select>
                </div>
                <!-- /.form-group -->
                <div class="col-md-6">
                  <label for="amount"><?php echo lang( 'amount' ); ?> <span class="required">*</span></label>
                  <input type="number" id="amount" class="form-control" step="0.01" name="amount" required>
                </div>
                <!-- /.form-group -->
              </div>
              <!-- /.form-row -->
              <div class="form-row">
                <div class="col-md-6 mt-3" id="create-invoice-wrapper">
                  <label for="create-invoice"><?php echo lang( 'create_invoice' ); ?></label>
                  <select id="create-invoice" class="form-control select2 search-disabled" name="create_invoice">
                    <option value="0"><?php echo lang( 'no' ); ?></option>
                    <option value="1"><?php echo lang( 'yes' ); ?></option>
                  </select>
                </div>
                <!-- /.form-group -->
              </div>
              <!-- /.form-row -->
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <button type="submit" class="btn btn-primary float-right text-sm">
                <i class="fas fa-check-circle mr-2"></i> <?php echo lang( 'submit' ); ?>
              </button>
            </div>
            <!-- /.card-footer -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
        <div class="col-md-3">
          <?php load_view( 'common/panel/user/user_credits' ); ?>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
      
      <input type="hidden" name="user_id" value="<?php echo intval( $user_id ); ?>">
    </form>
  </div>
  <!-- /.container-fluid -->
</div>
<!-- /.content -->