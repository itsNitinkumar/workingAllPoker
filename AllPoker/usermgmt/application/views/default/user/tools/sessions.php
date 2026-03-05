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
            <h3 class="card-title"><?php echo lang( 'manage_sessions' ); ?></h3>
            
            <?php if ( ! empty( $sessions ) ) {
              if ( $count > 1 ) { ?>
            <div class="card-tools ml-auto">
              <button class="btn btn-dark text-sm" data-toggle="modal" data-target="#logout-others">
                <i class="fas fa-minus-circle mr-2"></i> <?php echo lang( 'logout_my_others' ); ?>
              </button>
            </div>
            <!-- /.card-tools -->
            <?php }
            }?>
            
          </div>
          <!-- /.card-header -->
          <div class="card-body pt-0 pb-0 records-card-body">
            <div class="table-responsive">
              <table class="custom-table z-table table table-striped text-nowrap table-valign-middle mb-0">
                <thead class="records-thead">
                  <tr>
                    <th><?php echo lang( 'user_agent' ); ?></th>
                    <th class="text-right"><?php echo lang( 'ip_address' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'last_activity' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'logged_in' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'action' ); ?></th>
                  </tr>
                </thead>
                <tbody class="records-tbody text-sm">
                  <?php
                  if ( ! empty( $sessions ) )
                  {
                    foreach ( $sessions as $session ) { ?>
                      <tr id="record-<?php echo html_escape( $session->id ); ?>">
                        <td>
                          <h5><?php echo ( ! empty( $session->platform ) ) ? html_escape( $session->platform ) : lang( 'unknown' ); ?></h5>
                          <?php echo ( ! empty( $session->browser ) ) ? html_escape( $session->browser ) : lang( 'unknown' ); ?>
                          
                          <?php if ( $session->token == get_session( USER_TOKEN ) && $count > 1 ) { ?>
                            <strong class="text-primary">(<?php echo lang( 'current_device' ); ?>)</strong>
                          <?php } ?>
                        </td>
                        <td class="text-right"><?php echo html_escape( $session->ip_address ); ?></td>
                        
                        <td class="text-right">
                          <?php
                          if ( ! empty( $session->last_activity ) )
                          {
                              echo get_date_time_by_timezone( html_escape( $session->last_activity ) );
                          }
                          else
                          {
                              echo lang( 'n_a' );
                          }
                          ?>
                        </td>
                        
                        <td class="text-right"><?php echo get_date_time_by_timezone( html_escape( $session->logged_in_at ) ); ?></td>
                        <td class="text-right">
                          <button class="btn btn-sm btn-danger tool" data-id="<?php echo html_escape( $session->id ); ?>" data-toggle="modal" data-target="#delete">
                            <i class="fas fa-trash tool-c"></i>
                          </button>
                        </td>
                      </tr>
                    <?php }
                  } else { ?>
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

<?php load_modals( ['user/delete_user_session', 'user/logout_my_others'] ); ?>