<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-12">
        <form class="z-form" action="<?php admin_action( 'settings/support' ); ?>" method="post" data-csrf="manual">
          <div class="response-message"><?php echo alert_message(); ?></div>
          <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <h3 class="card-title"><?php echo lang( 'support_settings' ); ?></h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="form-group">
                <label for="contact-form"><?php echo lang( 'contact_form' ); ?></label>
                <select id="contact-form" class="form-control select2 search-disabled" name="cu_enable_form">
                  <option value="0" <?php echo select_single( 0, db_config( 'cu_enable_form' ) ); ?>><?php echo lang( 'disable' ); ?></option>
                  <option value="1" <?php echo select_single( 1, db_config( 'cu_enable_form' ) ); ?>><?php echo lang( 'enable' ); ?></option>
                </select>
              </div>
              <!-- /.form-group -->
              <div class="form-group">
                <label for="contact-email"><?php echo lang( 'email_address' ); ?></label>
                <input type="email" id="contact-email" class="form-control" name="cu_email_address" value="<?php echo html_escape( db_config( 'cu_email_address' ) ); ?>">
                <small class="form-text text-muted"><?php echo lang( 'cm_email_tip' ); ?></small>
              </div>
              <!-- /.form-group -->
              <div class="form-group">
                <label for="notify-tickets-replies"><?php echo lang( 'notify_tickets_replies' ); ?></label>
                <select id="notify-tickets-replies" class="form-control select2 search-disabled" name="sp_notify_replies">
                  <option value="0" <?php echo select_single( 0, db_config( 'sp_notify_replies' ) ); ?>><?php echo lang( 'disable' ); ?></option>
                  <option value="1" <?php echo select_single( 1, db_config( 'sp_notify_replies' ) ); ?>><?php echo lang( 'enable' ); ?></option>
                </select>
              </div>
              <!-- /.form-group -->
              <label for="tickets"><?php echo lang( 'tickets_module' ); ?></label>
              <select id="tickets" class="form-control select2 search-disabled" name="sp_enable_tickets">
                <option value="0" <?php echo select_single( 0, db_config( 'sp_enable_tickets' ) ); ?>><?php echo lang( 'disable' ); ?></option>
                <option value="1" <?php echo select_single( 1, db_config( 'sp_enable_tickets' ) ); ?>><?php echo lang( 'enable' ); ?></option>
              </select>
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