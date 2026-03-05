<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col">
        <div class="not-in-form">
          <div class="response-message"><?php echo alert_message(); ?></div>
        </div>
        <!-- /.not-in-form -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
    <div class="row">
      <div class="col-lg-8">
        <div class="card z-card">
          <div class="card-body">
            <span class="badge badge-primary"><?php printf( lang( 'ticket_no' ), html_escape( $ticket->id ) ); ?></span>
            <h3 class="mt-2"><?php echo html_escape( $ticket->subject ); ?></h3>
            <p>
              <?php echo nl2br( make_text_links( replace_some_with_actuals( html_escape( $ticket->message ) ) ) ); ?>
              
              <?php if ( ! empty( $ticket->attachment_name ) ) { ?>
                <span class="mt-2 d-block"><?php echo lang( 'attachment' ); ?>:</span>
                <a href="<?php echo attachments_uploads( html_escape( $ticket->attachment ) ); ?>" class="text-primary fs-sm" download>
                  <?php echo html_escape( long_to_short_name( $ticket->attachment_name ) ); ?>
                </a>
              <?php } ?>
            </p>
            <hr>
            <?php if ( ! empty( $replies ) ) {
              foreach ( $replies as $reply ) { ?>
                <div class="reply-message">
                  <div class="replier mb-2">
                    <?php if ( ! empty( $reply->user_picture ) ) { ?>
                      <img src="<?php echo user_picture( html_esc_url( $reply->user_picture ) ); ?>" class="img-circle" alt="User Image">
                      <span class="name"><?php echo html_escape( $reply->first_name . ' ' . $reply->last_name ); ?></span>
                    <?php } else { ?>
                      <img src="<?php echo user_picture( DEFAULT_USER_IMG ); ?>" class="img-circle" alt="User Image">
                      <span class="name"><?php echo lang( 'user_deleted' ); ?></span>
                    <?php } ?>
                  </div>
                  <!-- /.replier -->
                  <p class="mb-2">
                    <?php echo nl2br( make_text_links( replace_some_with_actuals( html_escape( $reply->message ) ) ) ); ?>
                    
                    <?php if ( ! empty( $reply->attachment_name ) ) { ?>
                      <span class="mt-2 d-block"><?php echo lang( 'attachment' ); ?>:</span>
                      <a href="<?php echo attachments_uploads( html_escape( $reply->attachment ) ); ?>" class="text-primary fs-sm" download>
                        <?php echo html_escape( long_to_short_name( $reply->attachment_name ) ); ?>
                      </a>
                    <?php } ?>
                  </p>
                  <div class="fs-sm text-secondary mb-4">
                    <i class="nav-icon far fa-clock"></i>
                    <?php echo get_date_time_by_timezone( html_escape( $reply->replied_at ) ); ?>
                  </div>
                  <!-- /.replied-at -->
                  <div class="btn-group">
                    <button class="btn btn-sm btn-primary get-data-tool" data-source="<?php admin_action( 'support/edit_ticket_reply' ); ?>" data-id="<?php echo html_escape( $reply->id ); ?>">
                      <span class="fas fa-edit get-data-tool-c"></span>
                    </button>
                    <button class="btn btn-sm btn-danger tool" data-id="<?php echo html_escape( $reply->id ); ?>" data-toggle="modal" data-target="#delete">
                      <i class="fas fa-trash tool-c"></i>
                    </button>
                  </div>
                  <!-- /.btn-group -->
                </div>
                <!-- /.reply-message -->
              <?php }
              } else { ?>
              <div class="text-center">
                <span class="d-block text-secondary"><?php echo lang( 'no_replies' ); ?></span>
              </div>
            <?php } ?>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
        <div class="card">
          <div class="card-body">
            <?php if ( $ticket->status != 0 ) { ?>
              <form class="z-form" action="<?php admin_action( 'support/add_reply' ); ?>" method="post" enctype="multipart/form-data" data-csrf="manual">
                <div class="response-message"></div>
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="form-group">
                  <label for="your-reply"><?php echo lang( 'your_reply' ); ?> <span class="required">*</span></label>
                  <textarea id="your-reply" class="form-control" name="reply" rows="3" required></textarea>
                </div>
                <!-- /.form-group -->
                <div class="form-group">
                  <label for="attachment"><?php echo lang( 'attach_file_opt' ); ?></label>
                  <input type="file" class="d-block" id="attachment" name="attachment" accept="<?php echo ALLOWED_IMG_EXT_HTML; ?>">
                  <small class="form-text text-muted"><?php echo lang( 'attach_file_tip' ); ?></small>
                </div>
                <!-- /.form-group -->
                <button type="submit" class="btn btn-primary float-right text-sm">
                  <i class="fas fa-check-circle mr-2"></i> <?php echo lang( 'submit' ); ?>
                </button>
                
                <input type="hidden" name="id" value="<?php echo html_escape( $ticket->id ); ?>">
              </form>
            <?php } else { ?>
              <div class="text-center">
                <span class="d-block"><?php echo lang( 'ticket_closed_msg' ); ?></span>
              </div>
            <?php } ?>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
      <div class="col-lg-4">
        <div class="card">
          <div class="card-body">
            <ul class="list-group">
              <li class="list-group-item">
                <?php echo lang( 'status' ); ?>:
                <span class="float-right badge badge-primary">
                  <?php manage_ticket_status( $ticket->status ); ?>
                </span>
              </li>
              <li class="list-group-item">
                <?php echo lang( 'reported_by' ); ?>:
                
                <?php if ( ! empty( $ticket->first_name ) ) { ?>
                  
                  <?php if ( $this->zuser->has_permission( 'users' ) ) { ?>
                    <a class="float-right" href="<?php echo env_url( 'admin/users/edit_user/' . html_escape( $ticket->user_id ) ); ?>" target="_blank">
                      <?php echo html_escape( $ticket->first_name . ' ' . $ticket->last_name ); ?>
                    </a>
                  <?php } else { ?>
                    <span class="float-right"><?php echo html_escape( $ticket->first_name . ' ' . $ticket->last_name ); ?></span>
                  <?php } ?>
                  
                <?php } else { ?>
                  <span class="float-right"><?php echo lang( 'user_deleted' ); ?></span>
                <?php } ?>
              </li>
              <li class="list-group-item">
                <?php echo lang( 'category' ); ?>:
                <span class="float-right">
                  <?php
                  if ( ! empty( $ticket->category ) )
                  {
                      echo html_escape( $ticket->category );
                  }
                  else
                  {
                      echo lang( 'unknown' );
                  }
                  ?>
                </span>
              </li>
              <li class="list-group-item">
                <?php echo lang( 'priority' ); ?>:
                <span class="float-right">
                  <?php echo lang( html_escape( $ticket->priority ) ); ?>
                </span>
              </li>
              <li class="list-group-item">
                <?php echo lang( 'updated' ); ?>:
                <span class="float-right"><?php manage_updated_at( html_escape( $ticket->updated_at ) ); ?></span>
              </li>
              <li class="list-group-item">
                <?php echo lang( 'created' ); ?>:
                <span class="float-right">
                  <?php echo get_date_time_by_timezone( html_escape( $ticket->created_at ) ); ?>
                </span>
              </li>
            </ul>
            <?php if ( $ticket->status != 0 ) { ?>
              <form class="z-form" method="post" action="<?php admin_action( 'support/close_ticket' ); ?>" data-csrf="manual">
                <div class="response-message c-alert-spacing"></div>
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <button type="submit" class="float-right mt-3 btn btn-danger btn-sm">
                  <i class="fas fa-times-circle mr-2"></i> <?php echo lang( 'close_ticket' ); ?>
                </button>
                
                <input type="hidden" name="id" value="<?php echo html_escape( $ticket->id ); ?>">
              </form>
            <?php } else { ?>
              <form class="z-form" method="post" action="<?php admin_action( 'support/reopen_ticket' ); ?>" data-csrf="manual">
                <div class="response-message c-alert-spacing"></div>
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <button type="submit" class="float-right mt-3 btn btn-success btn-sm">
                  <i class="fas fa-envelope-open-text mr-2"></i> <?php echo lang( 'reopen_ticket' ); ?>
                </button>
                
                <input type="hidden" name="id" value="<?php echo html_escape( $ticket->id ); ?>">
              </form>
            <?php } ?>
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

<?php load_modals( ['delete', 'read_lg'] ); ?>