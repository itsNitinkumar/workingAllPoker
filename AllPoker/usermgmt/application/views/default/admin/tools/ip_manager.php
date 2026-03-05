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
            <h3 class="card-title"><?php echo lang( 'ip_manager' ); ?></h3>
            <div class="card-tools ml-auto">
              <button class="btn btn-success text-sm" data-toggle="modal" data-target="#block-ip-address">
                <i class="fas fa-plus-circle mr-2"></i> <?php echo lang( 'block_ip_address' ); ?>
              </button>
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
                    <th class="th-2"><?php echo lang( 'ip_address' ); ?></th>
                    <th><?php echo lang( 'reason' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'blocked' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'action' ); ?></th>
                  </tr>
                </thead>
                <tbody class="records-tbody text-sm">
                  <?php
                  if ( ! empty( $ips ) )
                  {
                    foreach ( $ips as $ip ) {
                      $id = $ip->id; ?>
                      <tr id="record-<?php echo html_escape( $id ); ?>">
                        <td><?php echo html_escape( $ip->id ); ?></td>
                        <td>
                          <?php if ( db_config( 'ipinfo_token' ) !== '' ) { ?>
                            <span class="mr-1 badge badge-success get-data-tool" data-source="<?php admin_action( 'tools/ip_geolocation' ); ?>" data-id="<?php echo html_escape( $ip->ip_address ); ?>">
                              <?php echo lang( 'geolocation_data' ); ?>
                            </span>
                          <?php } ?>
                          
                          <?php echo html_escape( $ip->ip_address ); ?>
                        </td>
                        <td>
                          <?php if ( ! empty( $ip->reason ) ) { ?>
                             <?php echo html_escape( short_text( $ip->reason ) ); ?>
                             
                             <?php if ( is_increased_short_text( $ip->reason ) ) { ?>
                              <span class="badge badge-success get-data-tool" data-source="<?php admin_action( 'tools/ip_blocking_reason' ); ?>" data-id="<?php echo html_escape( $id ); ?>">
                                <?php echo lang( 'read_more' ); ?>
                              </span>
                            <?php } ?>
                          <?php }
                          else
                          {
                              echo lang( 'not_mentioned' );
                          }
                          ?>
                        </td>
                        <td class="text-right"><?php echo get_date_time_by_timezone( html_escape( $ip->blocked_at ) ); ?></td>
                        <td class="text-right">
                          <button class="btn btn-sm btn-danger tool" data-id="<?php echo html_escape( $ip->id ); ?>" data-toggle="modal" data-target="#delete">
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

<?php load_modals( ['admin/block_ip_address', 'read_lg', 'delete'] ); ?>