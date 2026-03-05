<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="content">
  <div class="container-fluid">
    <form class="z-form" action="<?php admin_action( 'users/update_user' ); ?>" method="post" enctype="multipart/form-data" data-csrf="manual">
      <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
      <div class="row">
        <div class="col">
          <div class="response-message"><?php echo alert_message(); ?></div>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
      <div class="row">
        <div class="col-xl-9">
          <div class="card collapsed-card">
            <div class="card-header">
              <h3 class="card-title"><?php echo lang( 'activity_and_api' ); ?></h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-plus"></i>
                </button>
              </div>
              <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <ul class="list-group">
                <li class="list-group-item">
                  <span>
                    <strong><?php echo lang( 'api_key' ); ?>:</strong>
                    <?php
                    if ( ! empty( $user->restful_api_key ) )
                    {
                        echo html_escape( $user->restful_api_key );
                    }
                    else
                    {
                        echo lang( 'n_a' );
                    }
                    ?>
                  </span>
                  
                  <?php if ( empty( $user->restful_api_key ) ) { ?>
                    <button type="button" class="d-block btn btn-success text-sm mt-3" data-toggle="modal" data-target="#generate-api-key">
                      <i class="fas fa-key mr-2"></i> <?php echo lang( 'generate' ); ?>
                    </button>
                  <?php } else { ?>
                    <button type="button" class="d-block btn btn-danger text-sm mt-3" data-toggle="modal" data-target="#reset-api-key">
                      <i class="fas fa-key mr-2"></i> <?php echo lang( 'reset' ); ?>
                    </button>
                  <?php } ?>
                  
                </li>
                <li class="list-group-item">
                  <span>
                    <strong><?php echo lang( 'last_activity' ); ?>:</strong>
                    
                    <?php
                    if ( ! empty( $user->last_activity ) )
                    {
                        echo get_date_time_by_timezone( html_escape( $user->last_activity ) );
                    }
                    else
                    {
                        echo lang( 'n_a' );
                    }
                    ?>
                  </span>
                </li>
                <li class="list-group-item">
                  <span>
                    <strong><?php echo lang( 'last_login' ); ?>:</strong>
                    <?php
                    if ( ! empty( $user->last_login ) )
                    {
                        echo get_date_time_by_timezone( html_escape( $user->last_login ) );
                    }
                    else
                    {
                        echo lang( 'n_a' );
                    }
                    ?>
                  </span>
                </li>
                <li class="list-group-item">
                  <span>
                    <strong><?php echo lang( 'last_login_interface' ); ?>:</strong>
                    <?php
                    if ( ! empty( $user->last_login ) )
                    {
                        echo lang( interface_key( $user->last_login_interface ) );
                    }
                    else
                    {
                        echo lang( 'n_a' );
                    }
                    ?>
                  </span>
                </li>
              </ul>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
          <?php load_view( 'common/panel/user/user_details' ); ?>
        </div>
        <!-- /.col -->
        <div class="col-xl-3">
          <?php if ( $user->id == 1 ) { ?>
            <div class="alert alert-info">
              <p><?php echo lang( 'u_change_not_allowed' ); ?></p>
            </div>
            <!-- /.alert -->
          <?php } ?>
          <div class="card">
            <div class="card-header">
              <h3 class="card-title"><?php echo lang( 'action' ); ?></h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <button type="submit" class="btn btn-primary btn-block text-sm">
                <i class="fas fa-check-circle mr-2"></i> <?php echo lang( 'update' ); ?>
              </button>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title"><?php echo lang( 'status' ); ?></h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <select id="user-account-status" class="form-control select2 search-disabled" name="status">
                <option value="1" <?php echo select_single( 1, $user->status ); ?>><?php echo lang( 'active' ); ?></option>
                <option value="0" <?php echo select_single( 0, $user->status ); ?>><?php echo lang( 'banned' ); ?></option>
              </select>
              
              <?php if ( $user->status == 1 ) { ?>
                <textarea id="banning-reason" class="form-control mt-2 d-none" name="reason" placeholder="<?php echo lang( 'reason_optional' ); ?>" rows="5"></textarea>
              <?php } else if ( ! empty( $user->reason ) ) { ?>
                <div class="card mb-0 mt-3 card-danger">
                  <div class="card-header">
                    <h3 class="card-title"><?php echo lang( 'banning_reason' ); ?></h3>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body">
                    <p><?php echo nl2br( html_escape( $user->reason ) ); ?></p>
                  </div>
                  <!-- /.card-body -->
                </div>
                <!-- /.card -->
              <?php } ?>
              
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
          
          <div class="card">
            <div class="card-header">
              <h3 class="card-title"><?php echo lang( 'email_verified' ); ?></h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="icheck icheck-primary d-inline-block mr-2">
                <input type="radio" name="email_verified" id="email-verified-1" value="1" <?php echo check_single( 1, $user->is_verified ); ?>>
                <label for="email-verified-1"><?php echo lang( 'yes' ); ?></label>
              </div>
              <!-- /.icheck -->
              <div class="icheck icheck-primary d-inline-block">
                <input type="radio" name="email_verified" id="email-verified-0" value="0" <?php echo check_single( 0, $user->is_verified ); ?>>
                <label for="email-verified-0"><?php echo lang( 'no' ); ?></label>
              </div>
              <!-- /.icheck -->
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title"><?php echo lang( 'password' ); ?></h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="<?php echo lang( 'password' ); ?>">
              </div>
              <!-- /.form-group -->
              <input type="password" class="form-control" name="retype_password" placeholder="<?php echo lang( 'retype_password' ); ?>">
              <small class="form-text text-muted mt-1"><?php echo lang( 'leave_password_msg' ); ?></small>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title"><?php echo lang( 'role' ); ?></h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <select class="form-control select2 search-disabled" name="role">
                <?php if ( ! empty( $roles ) ) {
                  foreach ( $roles as $role ) {
                    $id = $role->id; ?>
                  <option value="<?php echo html_escape( $id ); ?>" <?php echo select_single( $id, $user->role ); ?>><?php echo html_escape( $role->name ); ?></option>
                <?php }
                } ?>
              </select>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
          
          <?php load_view( 'common/panel/user/right_of_profile_settings' ); ?>
          
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
      
      <input type="hidden" name="id" value="<?php echo html_escape( $user->id ); ?>">
    </form>
  </div>
  <!-- /.container-fluid -->
</div>
<!-- /.content -->

<?php load_modals( ['user/view_profile_picture', 'admin/delete_profile_picture', 'admin/generate_api_key', 'admin/reset_api_key'] ); ?>