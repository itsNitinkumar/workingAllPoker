<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="content">
  <div class="container-fluid">
    <div class="row dashboard">
      <div class="col-sm-12">
        <div class="not-in-form">
          <div class="response-message"><?php echo alert_message(); ?></div>
        </div>
        <!-- /.not-in-form -->
        <?php if ( $this->zuser->has_permission( 'users' ) ) { ?>
          <div class="row">
            <div class="col-12 col-sm-6 col-md-3">
              <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-people-arrows"></i></span>
                <div class="info-box-content">
                  <a class="info-box-text" href="<?php echo env_url( 'admin/users/manage?filter=social' ); ?>"><?php echo lang( 'social_users' ); ?></a>
                  <span class="info-box-number"><?php echo html_escape( $dashboard['social_users'] ); ?></span>
                </div>
                <!-- /.info-box-content -->
              </div>
              <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-12 col-sm-6 col-md-3">
              <div class="info-box mb-3">
                <span class="info-box-icon bg-secondary"><i class="fas fa-user-clock"></i></span>
                <div class="info-box-content">
                  <a class="info-box-text" href="<?php echo env_url( 'admin/users/manage?filter=new_tfhrs' ); ?>"><?php echo lang( 'new_within_24hrs' ); ?></a>
                  <span class="info-box-number"><?php echo html_escape( $dashboard['new_within_24hrs'] ); ?></span>
                </div>
                <!-- /.info-box-content -->
              </div>
              <!-- /.info-box -->
            </div>
            <!-- /.col -->

            <!-- Fix for Small Devices Only: -->
            <div class="clearfix hidden-md-up"></div>
            
            <div class="col-12 col-sm-6 col-md-3">
              <div class="info-box mb-3">
                <span class="info-box-icon bg-success"><i class="fas fa-plug"></i></span>
                <div class="info-box-content">
                  <a class="info-box-text" href="<?php echo env_url( 'admin/users/manage?filter=online_today' ); ?>"><?php echo lang( 'online_today' ); ?></a>
                  <span class="info-box-number"><?php echo html_escape( $dashboard['online_today'] ); ?></span>
                </div>
                <!-- /.info-box-content -->
              </div>
              <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-12 col-sm-6 col-md-3">
              <div class="info-box mb-3">
                <span class="info-box-icon bg-warning"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                  <a class="info-box-text" href="<?php echo env_url( 'admin/users/manage' ); ?>"><?php echo lang( 'total_users' ); ?></a>
                  <span class="info-box-number"><?php echo html_escape( $dashboard['total_users'] ); ?></span>
                </div>
                <!-- /.info-box-content -->
              </div>
              <!-- /.info-box -->
            </div>
            <!-- /.col -->
          </div>
          <div class="row">
            <div class="col-lg-8">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title"><?php echo lang( 'statistics' ); ?></h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <?php $counts = json_decode( $dashboard['recent_users_stats'], true ); ?>
                  <canvas id="chart"></canvas>
                  <input type="hidden" class="users_stats_months" value="<?php echo implode( ',', html_escape( array_keys( $counts ) ) ); ?>">
                  <input type="hidden" class="users_stats_counts" value="<?php echo implode( ',', html_escape( array_values( $counts ) ) ); ?>">
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->
            </div>
            <!-- /.col -->
            <div class="col-lg-4">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title"><?php echo lang( 'recently_registered' ); ?></h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <?php if ( ! empty( $users ) ) { ?>
                    <?php foreach ( $users as $user ) { ?>
                      <div class="user-list-box">
                        <div class="d-inline-block position-relative mr-2">
                          <img src="<?php echo user_picture( html_esc_url( $user->picture ) ); ?>" class="img-circle elevation-1 profile-pic-sm" alt="<?php echo html_escape( $user->username ); ?>">
                          
                          <?php if ( $user->is_online == 0 ) { ?>
                            <span class="connectivity-status bg-secondary" data-toggle="tooltip" data-placement="left" title="<?php echo lang( 'offline' ); ?>"></span>
                          <?php } else { ?>
                            <span class="connectivity-status bg-success" data-toggle="tooltip" data-placement="left" title="<?php echo lang( 'online' ); ?>"></span>
                          <?php } ?>
                          
                        </div>
                        <div class="d-inline-block align-middle">
                          <a class="text-dark" href="<?php echo env_url( 'admin/users/edit_user/' . html_escape( $user->id ) ); ?>">
                            <?php echo html_escape( long_to_short_name( $user->first_name . ' ' . $user->last_name ) ); ?>
                          </a>
                          <small class="d-block text-muted"><?php echo html_escape( $user->role_name ); ?></small>
                        </div>
                        <small class="float-right text-muted db-reg-at"><?php echo get_date_time_by_timezone( html_escape( $user->registered_at ) ); ?></small>
                      </div>
                      <!-- /.user-list-box -->
                    <?php } ?>
                  <?php } ?>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
        <?php } else { ?>
          
          <!-- User -->
          
        <?php } ?>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container-fluid -->
</div>
<!-- /.content -->