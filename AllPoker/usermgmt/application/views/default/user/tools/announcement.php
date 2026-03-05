<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <?php if ( ! empty( $announcement ) ) { ?>
        <div class="col-md-6 offset-md-3">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title"><?php echo html_escape( $announcement->subject ); ?></h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <p><?php echo nl2br( html_escape( $announcement->announcement ) ); ?></p>
              <p class="text-muted mt-3 text-sm">
                <i class="far fa-clock"></i>
                <?php echo get_date_time_by_timezone( html_escape( $announcement->created_at ) ); ?>
              </p>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      <?php } ?>
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container-fluid -->
</div>
<!-- /.content -->