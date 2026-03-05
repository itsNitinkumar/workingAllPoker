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
              <table class="custom-table table table-striped text-nowrap table-valign-middle mb-0">
                <thead class="records-thead">
                  <tr>
                    <th class="th-2"><?php echo lang( 'transaction_id' ); ?></th>
                    <th><?php echo lang( 'amount' ); ?></th>
                    <th class="text-right"><?php echo lang( 'status' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'performed' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'invoice' ); ?></th>
                  </tr>
                </thead>
                <tbody class="text-sm">
                  <?php
                  
                  if ( ! empty( $payments_log ) )
                  {
                    foreach ( $payments_log as $log ) { ?>
                      <tr>
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
                          <?php if ( $log->create_invoice ) { ?>
                            <a href="<?php echo env_url( 'user/payment/invoice/' . html_escape( $log->hash ) ); ?>" class="btn btn-sm btn-primary" target="_blank">
                              <i class="fas fa-file-invoice mr-2"></i> <?php echo lang( 'get_invoice' ); ?>
                            </a>
                          <?php } else { ?>
                            <button class="btn btn-sm btn-primary" disabled>
                              <i class="fas fa-file-invoice mr-2"></i> <?php echo lang( 'not_available' ); ?>
                            </button>
                          <?php } ?>
                        </td>
                      </tr>
                      <?php }
                    } else {
                  ?>
                    <tr>
                      <td colspan="5"><?php echo lang( 'no_records_found' ); ?></td>
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