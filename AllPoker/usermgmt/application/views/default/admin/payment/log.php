<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-12">
        <div class="not-in-form">
          <div class="response-message"></div>
        </div>
        <!-- /.not-in-form -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><?php echo lang( 'payments_log' ); ?></h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body pt-0 pb-0 records-card-body">
            <div class="table-responsive">
              <table class="custom-table z-table table table-striped text-nowrap table-valign-middle mb-0">
                <thead class="records-thead">
                  <tr>
                    <th class="th-1"><?php echo lang( 'id' ); ?></th>
                    <th class="th-2"><?php echo lang( 'transaction_id' ); ?></th>
                    <th><?php echo lang( 'amount' ); ?></th>
                    <th class="text-right"><?php echo lang( 'status' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'performed' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'actions' ); ?></th>
                  </tr>
                </thead>
                <tbody class="records-tbody z-records-asc text-sm">
                  <?php
                  
                  if ( ! empty( $payments_log ) )
                  {
                    foreach ( $payments_log as $log ) {
                      $id = $log->id; ?>
                      <tr id="record-<?php echo html_escape( $id ); ?>">
                        <td><?php echo html_escape( $id ); ?></td>
                        <td>
                          <?php
                          if ( ! empty( $log->transaction_id ) )
                          {
                              echo html_escape( $log->transaction_id );
                          }
                          else
                          {
                              echo lang( 'n_a' );
                          }
                          ?>
                        </td>
                        <td><?php echo html_escape( $log->amount . ' ' . $log->code ); ?></td>
                        <td class="text-right">
                          <?php if ( $log->status == 'succeeded' ) { ?><span class="badge badge-success">
                          <?php } else { ?><span class="badge badge-warning"><?php } ?>
                          
                          <?php echo html_escape( $log->status ); ?>
                          </span>
                        </td>
                        <td class="text-right"><?php echo get_date_time_by_timezone( html_escape( $log->performed_at ) ); ?></td>
                        <td class="text-right">
                          <div class="btn-group">
                            <button class="btn btn-sm btn-primary get-data-tool" data-source="<?php admin_action( 'payment/log_details' ); ?>" data-id="<?php echo html_escape( $id ); ?>">
                              <span class="fas fa-eye get-data-tool-c"></span>
                            </button>
                            <button class="btn btn-sm btn-danger tool" data-id="<?php echo html_escape( $id ); ?>" data-toggle="modal" data-target="#delete">
                              <i class="fas fa-trash tool-c"></i>
                            </button>
                          </div>
                          <!-- /.btn-group -->
                        </td>
                      </tr>
                      <?php }
                    } else {
                  ?>
                    <tr id="record-0">
                      <td colspan="6"><?php echo lang( 'no_records_found' ); ?></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
            <!-- /.table-responsive -->
            
            <div class="clearfix"><?php echo $pagination; ?></div>
            
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container-fluid -->
</div>
<!-- /.content -->

<?php load_modals( ['read', 'delete'] ); ?>