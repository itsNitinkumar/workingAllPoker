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
            <h3 class="card-title"><?php echo lang( 'activities_log' ); ?></h3>
            
            <?php if ( ! empty( $activities ) && ! get( 'ip_address' ) ) { ?>
              <div class="card-tools ml-auto">
                <button class="btn btn-danger text-sm" data-toggle="modal" data-target="#clear-log">
                  <i class="fas fa-trash mr-2"></i> <?php echo lang( 'clear_log' ); ?>
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
                    <th><?php echo lang( 'activity' ); ?></th>
                    <th class="text-right"><?php echo lang( 'user' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'ip_address' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'performed' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'detail' ); ?></th>
                  </tr>
                </thead>
                <tbody class="records-tbody text-sm">
                  <?php
                  if ( ! empty( $activities ) )
                  {
                    foreach ( $activities as $activity ) { ?>
                      <tr id="record-<?php echo html_escape( $activity->id ); ?>">
                        <td><?php echo html_escape( $activity->id ); ?></td>
                        <td><?php echo html_escape( $activity->activity );?></td>
                        <td class="text-right">
                          <?php
                          if ( ! empty( $activity->user_id ) )
                          {
                              echo '<a href="' . env_url( 'admin/users/edit_user/' . html_escape( $activity->user_id ) ) . '" target="_blank">';
                              echo html_escape( $activity->first_name . ' ' . $activity->last_name );
                              echo '</a>';
                          }
                          else
                          {
                              echo lang( 'guest' );
                          }
                          ?>
                        </td>
                        <td class="text-right">
                          <?php if ( ! get( 'ip_address' ) ) { ?>
                            <a href="<?php echo env_url( 'admin/tools/activities_log' ); ?>?ip_address=<?php echo html_escape( $activity->ip_address ); ?>" target="blank">
                              <?php echo html_escape( $activity->ip_address ); ?>
                            </a>
                          <?php } else {
                            echo html_escape( $activity->ip_address );
                          } ?>

                          <?php if ( db_config( 'ipinfo_token' ) !== '' ) { ?>
                            <span class="ml-1 badge badge-success get-data-tool" data-source="<?php admin_action( 'tools/ip_geolocation' ); ?>" data-id="<?php echo html_escape( $activity->ip_address ); ?>">
                              <?php echo lang( 'geolocation_data' ); ?>
                            </span>
                          <?php } ?>
                        </td>
                        <td class="text-right"><?php echo get_date_time_by_timezone( html_escape( $activity->performed_at ) ); ?></td>
                        <td class="text-right">
                          <?php if ( ! empty( $activity->detail ) ) { ?>
                            <button type="button" class="ml-2 btn btn-primary btn-sm" data-toggle="popover" data-placement="left" data-content="<?php echo html_escape( $activity->detail ); ?>">
                          <?php } else { ?>
                            <button type="button" class="ml-2 btn btn-primary btn-sm" disabled>
                          <?php } ?>
                            <i class="fas fa-info-circle"></i>
                          </button>
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

<?php load_modals( ['admin/clear_log', 'read_lg'] ); ?>