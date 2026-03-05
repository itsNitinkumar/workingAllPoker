<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-12">
        <div class="response-message"><?php echo alert_message(); ?></div>
        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h3 class="card-title"><?php echo lang( 'tickets' ); ?></h3>
            <div class="card-tools ml-auto">
              <a href="<?php echo env_url( 'user/support/create_ticket' ); ?>" class="btn btn-success text-sm">
                <i class="fas fa-plus-circle mr-2"></i> <?php echo lang( 'create_ticket' ); ?>
              </a>
            </div>
            <!-- /.card-tools -->
          </div>
          <!-- /.card-header -->
          <div class="card-body pt-0 pb-0 records-card-body">
            <div class="table-responsive">
              <table class="custom-table table table-striped text-nowrap table-valign-middle mb-0">
                <thead class="records-thead">
                  <tr>
                    <th class="th-2"><?php echo lang( 'id' ); ?></th>
                    <th><?php echo lang( 'subject' ); ?></th>
                    <th class="text-right"><?php echo lang( 'status' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'updated' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'created' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'action' ); ?></th>
                  </tr>
                </thead>
                <tbody class="text-sm">
                  <?php
                  if ( ! empty( $tickets ) )
                  {
                    foreach ( $tickets as $ticket ) { ?>
                      <tr>
                        <td><?php echo html_escape( $ticket->id );?></td>
                        <td>
                          <?php echo html_escape( $ticket->subject );?>
                          
                          <?php if ( $ticket->is_read == 0 && $ticket->last_message_area === 'admin' ) { ?>
                            <span class="badge badge-danger ml-1"><?php echo lang( 'unread' ); ?></span>
                          <?php } ?>
                        </td>
                        <td class="text-right">
                          <span class="badge badge-primary"><?php manage_ticket_status( $ticket->status ); ?></span>
                        </td>
                        <td class="text-right"><?php manage_updated_at( html_escape( $ticket->updated_at ) ); ?></td>
                        <td class="text-right"><?php echo get_date_time_by_timezone( html_escape( $ticket->created_at ) ); ?></td>
                        <td class="text-right">
                          <a href="<?php echo env_url( 'user/support/ticket/' . html_escape( $ticket->id ) ); ?>" class="ml-2 btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i>
                          </a>
                        </td>
                      </tr>
                      <?php }
                    } else {
                  ?>
                    <tr>
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