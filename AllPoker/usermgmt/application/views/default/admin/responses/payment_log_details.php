<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="modal-header">
  <h5 class="modal-title"><?php echo lang( 'payment_log' ); ?></h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<!-- /.modal-header -->
<div class="modal-body">
  <ul class="list-group">
    <li class="list-group-item">
      <span class="font-weight-bold"><?php echo lang( 'transaction_id' ); ?>:</span>
      <span class="float-right">
        <?php
        if ( ! empty( $transaction_id ) )
        {
            echo html_escape( $transaction_id );
        }
        else
        {
            echo lang( 'n_a' );
        }
        ?>
      </span>
    </li>
    <li class="list-group-item">
      <span class="font-weight-bold"><?php echo lang( 'user' ); ?>:</span>
      <span class="float-right">
        <?php if ( ! empty( $email_address ) ) { ?>
          
          <?php if ( $this->zuser->has_permission( 'users' ) ) { ?>
            <a href="<?php echo env_url( 'admin/users/edit_user/' . html_escape( $user_id ) ); ?>" target="_blank"><?php echo html_escape( $email_address ); ?></a>
          <?php } else {
            echo html_escape( $email_address );
          } ?>
          
        <?php } else {
          echo lang( 'user_deleted' );
        } ?>
      </span>
    </li>
    <li class="list-group-item">
      <span class="font-weight-bold"><?php echo lang( 'payment_gateway' ); ?>:</span>
      <span class="float-right"><?php echo lang( html_escape( $gateway ) ); ?></span>
    </li>
    <li class="list-group-item">
      <span class="font-weight-bold"><?php echo lang( 'log_visible_to_user' ); ?>:</span>
      <span class="float-right badge badge-primary">
        <?php
        if ( $visible_to_user == 1 )
        {
            echo lang( 'yes' );
        }
        else
        {
            echo lang( 'no' );
        }
        ?>
      </span>
    </li>
    <li class="list-group-item">
      <span class="font-weight-bold"><?php echo lang( 'create_invoice' ); ?>:</span>
      <span class="float-right badge badge-primary">
        <?php
        if ( $create_invoice == 1 )
        {
            echo lang( 'yes' );
        }
        else
        {
            echo lang( 'no' );
        }
        ?>
      </span>
    </li>
    <li class="list-group-item">
      <span class="font-weight-bold"><?php echo lang( 'item_id' ); ?>:</span>
      <span class="float-right">
      <?php
        if ( ! empty( $item_id ) )
        {
            echo html_escape( $item_id );
        }
        else
        {
            echo lang( 'n_a' );
        }
        ?>
      </span>
    </li>
    <li class="list-group-item">
      <span class="font-weight-bold"><?php echo lang( 'item' ); ?>:</span>
      <span class="float-right"><?php echo html_escape( $item_name ); ?></span>
    </li>
    <li class="list-group-item">
      <span class="font-weight-bold"><?php echo lang( 'quantity' ); ?>:</span>
      <span class="float-right"><?php echo html_escape( $quantity ); ?></span>
    </li>
    <li class="list-group-item">
      <span class="font-weight-bold"><?php echo lang( 'amount' ); ?>:</span>
      <span class="float-right"><?php echo html_escape( $amount . ' ' . $code ); ?></span>
    </li>
    <li class="list-group-item">
      <span class="font-weight-bold"><?php echo lang( 'status' ); ?>:</span>
      <span class="float-right">
        <?php if ( $status == 'succeeded' ) { ?><span class="badge badge-success">
        <?php } else { ?><span class="badge badge-warning"><?php } ?>
        
        <?php echo html_escape( $status ); ?>
        </span>
      </span>
    </li>
    <li class="list-group-item">
      <span class="font-weight-bold"><?php echo lang( 'performed' ); ?>:</span>
      <span class="float-right"><?php echo get_date_time_by_timezone( html_escape( $performed_at ) ); ?></span>
    </li>
  </ul>
</div>
<!-- /.modal-body -->  