<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <?php if ( ! empty( $announcements ) ) {
        foreach ( $announcements as $announcement ) { ?>
        <div class="col-md-6 offset-md-3">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title"><?php echo html_escape( $announcement->subject ); ?></h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <p><?php echo html_escape( get_sized_text( $announcement->announcement, 180, true ) ); ?></p>
              <p class="text-muted mt-3 text-sm">
                <i class="far fa-clock"></i>
                <?php echo get_date_time_by_timezone( html_escape( $announcement->created_at ) ); ?>
              </p>
              
              <?php if ( is_increased_length( $announcement->announcement, 180 ) ) { ?>
                <hr><a class="btn btn-primary text-sm mt-1" href="<?php echo env_url( html_escape( 'user/tools/announcement/' . $announcement->id ) ); ?>">
                  <i class="fas fa-eye mr-2"></i> <?php echo lang( 'read_more' ); ?>
                </a>
              <?php } ?>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      <?php }
      } ?>
      
      <div class="col-md-6 offset-md-3 clearfix remove-ul-m">
        <?php echo $pagination; ?>
      </div>
      <!-- /.col -->
      
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container-fluid -->
</div>
<!-- /.content -->