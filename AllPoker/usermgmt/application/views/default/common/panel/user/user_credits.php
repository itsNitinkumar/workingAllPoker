<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h3 class="card-title"><?php echo lang( 'user_credits' ); ?></h3>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <ul class="list-group">
      <?php if ( ! empty( $credits ) ) {
        foreach ( $credits as $credit ) { ?>
          <li class="list-group-item">
            <span>
              <strong><?php echo html_escape( $credit->code ); ?>:</strong>
              <?php echo html_escape( $credit->credit ) + 0; ?>
            </span>
          </li>
        <?php }
      } else { ?>
        <li class="list-group-item text-center">
          <span class="text-muted"><?php echo lang( 'no_credits' ); ?></span>
        </li>
      <?php } ?>
    </ul>
  </div>
  <!-- /.card-body -->
</div>
<!-- /.card -->