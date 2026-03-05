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
          <div class="card-header d-flex align-items-center">
            <h3 class="card-title"><?php echo lang( 'replied_msgs' ); ?></h3>
            <div class="card-tools ml-auto">
              <a href="<?php echo env_url( 'admin/support/contact_messages/not_replied' ); ?>" class="btn btn-dark text-sm">
                <i class="fas fa-envelope mr-2"></i> <?php echo lang( 'not_replied_msgs' ); ?>
              </a>
            </div>
            <!-- /.card-tools -->
          </div>
          <!-- /.card-header -->
          <div class="card-body pt-0 pb-0 records-card-body">
            <div class="table-responsive">
              <table class="custom-table z-table table table-striped text-nowrap table-valign-middle mb-0">
                <thead class="records-thead">
                  <tr>
                    <th class="th-1"><?php echo lang( 'id' ); ?></th>
                    <th class="th-2"><?php echo lang( 'full_name' ); ?></th>
                    <th class="th-2"><?php echo lang( 'email_address' ); ?></th>
                    <th class="th-2"><?php echo lang( 'message' ); ?></th>
                    <th><?php echo lang( 'reply' ); ?></th>
                    <th class="text-right"><?php echo lang( 'replied' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'received' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'action' ); ?></th>
                  </tr>
                </thead>
                <tbody class="records-tbody text-sm">
                  <?php
                  if ( ! empty( $messages ) )
                  {
                    foreach ( $messages as $message ) {
                      $id = $message->id; ?>
                      <tr id="record-<?php echo html_escape( $id ); ?>">
                        <td><?php echo html_escape( $id ); ?></td>
                        <td><?php echo html_escape( $message->full_name ); ?></td>
                        <td><?php echo html_escape( $message->email_address ); ?></td>
                        <td>
                          <?php echo html_escape( short_text( $message->message ) ); ?>
                          
                          <?php if ( is_increased_short_text( $message->message ) ) { ?>
                            <span class="badge badge-success get-data-tool" data-source="<?php admin_action( 'support/contact_message/message' ); ?>" data-id="<?php echo html_escape( $id ); ?>">
                              <?php echo lang( 'read_more' ); ?>
                            </span>
                          <?php } ?>
                        </td>
                        <td>
                          <?php echo html_escape( short_text( $message->reply ) ); ?>
                          
                          <?php if ( is_increased_short_text( $message->reply ) ) { ?>
                            <span class="badge badge-success get-data-tool" data-source="<?php admin_action( 'support/contact_message/reply' ); ?>" data-id="<?php echo html_escape( $id ); ?>">
                              <?php echo lang( 'read_more' ); ?>
                            </span>
                          <?php } ?>
                        </td>
                        <td class="text-right"><?php echo get_date_time_by_timezone( html_escape( $message->replied_at ) ); ?></td>
                        <td class="text-right"><?php echo get_date_time_by_timezone( html_escape( $message->received_at ) ); ?></td>
                        <td class="text-right">
                          <button class="btn btn-sm btn-danger tool" data-id="<?php echo html_escape( $id ); ?>" data-toggle="modal" data-target="#delete">
                            <i class="fas fa-trash tool-c"></i>
                          </button>
                        </td>
                      </tr>
                      <?php }
                    } else {
                  ?>
                    <tr id="record-0">
                      <td colspan="8"><?php echo lang( 'no_records_found' ); ?></td>
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

<?php load_modals( ['delete', 'read'] ); ?>