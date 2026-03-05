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
            <h3 class="card-title"><?php printf( lang( 'user_activities_log' ), html_escape( $user_email_address ) ); ?></h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body pt-0 pb-0 records-card-body">
            <div class="table-responsive">
              <table class="custom-table z-table table table-striped text-nowrap table-valign-middle mb-0">
                <thead class="records-thead">
                  <tr>
                    <th><?php echo lang( 'activity' ); ?></th>
                    <th class="text-right"><?php echo lang( 'ip_address' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'performed' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'detail' ); ?></th>
                  </tr>
                </thead>
                <tbody class="text-sm">
                  <?php
                  if ( ! empty( $activities ) )
                  {
                    foreach ( $activities as $activity ) { ?>
                      <tr>
                        <td><?php echo html_escape( $activity->activity );?></td>
                        <td class="text-right">
                          <?php if ( ! get( 'ip_address' ) ) { ?>
                            <a href="<?php echo env_url( 'admin/users/activities_log/' . $this->uri->segment( 4 ) ); ?>?ip_address=<?php echo html_escape( $activity->ip_address ); ?>" target="blank">
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
                          <button type="button" class="ml-2 btn btn-primary btn-sm" data-toggle="popover" data-placement="left" data-content="<?php echo html_escape( $activity->detail ); ?>">
                            <i class="fas fa-info-circle"></i>
                          </button>
                        </td>
                      </tr>
                      <?php }
                    } else {
                  ?>
                    <tr>
                      <td colspan="4"><?php echo lang( 'no_records_found' ); ?></td>
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

<?php load_modals( 'read_lg' ); ?>