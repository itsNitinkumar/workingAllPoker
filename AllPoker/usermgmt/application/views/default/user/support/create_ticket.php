<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col">
        <div class="response-message"><?php echo alert_message(); ?></div>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
    <div class="row">
      <div class="col-sm-12">
        <form class="z-form" action="<?php user_action( 'support/create_ticket' ); ?>" method="post" enctype="multipart/form-data" data-csrf="manual">
          <div class="response-message"></div>
          <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <h3 class="card-title"><?php echo lang( 'create_ticket' ); ?></h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="subject"><?php echo lang( 'subject' ); ?> <span class="required">*</span></label>
                  <input type="text" id="subject" class="form-control" name="subject" required>
                </div>
                <!-- /.form-group -->
                <div class="form-group col-md-6">
                  <label for="priority"><?php echo lang( 'priority' ); ?> <span class="required">*</span></label>
                  <select id="priority" data-placeholder="<?php echo lang( 'choose_priority' ); ?>" class="form-control select2 search-disabled" name="priority" required>
                    <option></option>
                    <option value="low"><?php echo lang( 'low' ); ?></option>
                    <option value="medium"><?php echo lang( 'medium' ); ?></option>
                    <option value="high"><?php echo lang( 'high' ); ?></option>
                  </select>
                </div>
                <!-- /.form-group -->
              </div>
              <!-- /.form-row -->
              <div class="form-group">
                <label for="category"><?php echo lang( 'category' ); ?> <span class="required">*</span></label>
                <select id="category" data-placeholder="<?php echo lang( 'choose_category' ); ?>" class="form-control select2 search-disabled" name="category" required>
                  <option></option>
                  
                  <?php if ( ! empty( $categories ) ) {
                    foreach ( $categories as $category ) { ?>
                    <option value="<?php echo html_escape( $category->id ); ?>"><?php echo html_escape( $category->name ); ?></option>
                  <?php }
                  } ?>
                </select>
              </div>
              <!-- /.form-group -->
              <div class="form-group">
                <label for="message"><?php echo lang( 'message' ); ?> <span class="required">*</span></label>
                <textarea class="form-control" id="message" name="message" rows="6" required></textarea>
              </div>
              <!-- /.form-group -->
              <label for="attachment"><?php echo lang( 'attach_file_opt' ); ?></label>
              <input type="file" class="d-block" id="attachment" name="attachment" accept="<?php echo ALLOWED_IMG_EXT_HTML; ?>">
              <small class="form-text text-muted"><?php echo lang( 'attach_file_tip' ); ?></small>
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
        </form>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container-fluid -->
</div>
<!-- /.content -->