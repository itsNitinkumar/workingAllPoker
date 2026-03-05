<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-12">
        <div class="not-in-form">
          <div class="response-message"><?php echo alert_message(); ?></div>
        </div>
        <!-- /.not-in-form -->
        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h3 class="card-title"><?php echo lang( 'subscribers' ); ?></h3>
            
            <?php if ( ! empty( $subscribers ) ) { ?>
              <div class="card-tools ml-auto">
                <button class="btn btn-success text-sm" data-toggle="modal" data-target="#send-email-subscribers">
                  <i class="fas fa-paper-plane mr-2"></i> <?php echo lang( 'send_email' ); ?>
                </button>
              </div>
              <!-- /.card-tools -->
            <?php } ?>
            
          </div>
          <!-- /.card-header -->
          <div class="card-body pt-0 pb-0 records-card-body">
            <div class="table-responsive">
              <table class="custom-table z-table table table-striped text-nowrap table-valign-middle mb-0">
                <thead class="records-thead">
                  <tr>
                    <th class="th-1"><?php echo lang( 'id' ); ?></th>
                    <th><?php echo lang( 'email_address' ); ?></th>
                    <th class="text-right"><?php echo lang( 'confirmed' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'subscribed' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'action' ); ?></th>
                  </tr>
                </thead>
                <tbody class="records-tbody text-sm">
                  <?php
                  if ( ! empty( $subscribers ) )
                  {
                    foreach ( $subscribers as $subscriber ) { ?>
                      <tr id="record-<?php echo html_escape( $subscriber->id ); ?>">
                        <td><?php echo html_escape( $subscriber->id ); ?></td>
                        <td><?php echo html_escape( $subscriber->email_address ); ?></td>
                        <td class="text-right">
                          <?php
                          if ( ! empty( $subscriber->confirmed_at ) )
                          {
                              echo get_date_time_by_timezone( html_escape( $subscriber->confirmed_at ) );
                          }
                          else
                          {
                              echo lang( 'not_confirmed' );
                          }
                          ?>
                          <?php  ?>
                        </td>
                        <td class="text-right"><?php echo get_date_time_by_timezone( html_escape( $subscriber->subscribed_at ) ); ?></td>
                        <td class="text-right">
                          <button class="btn btn-sm btn-danger tool" data-id="<?php echo html_escape( $subscriber->id ); ?>" data-toggle="modal" data-target="#delete">
                            <i class="fas fa-trash tool-c"></i>
                          </button>
                        </td>
                      </tr>
                      <?php }
                    } else {
                  ?>
                    <tr id="record-0">
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

<?php load_modals( ['admin/send_email_subscribers', 'delete'] ); ?>