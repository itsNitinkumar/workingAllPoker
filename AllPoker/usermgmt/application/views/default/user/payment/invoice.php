<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<!DOCTYPE html>
<html>

<body>

<div class="main-wrapper">
  <div class="section-1">
    <div class="col-left">
      <h2 class="main-title"><?php echo html_escape( db_config( 'site_name' ) ); ?></h2>
      <span class="break margin-bottom-1">
        <strong><?php echo lang( 'invoice_number' ); ?>:</strong>
        <?php echo sprintf( '%06d', html_escape( $id ) ); ?>
      </span>
      <span>
        <strong><?php echo lang( 'date' ); ?>:</strong>
        <?php echo get_date_time_by_timezone( html_escape( $performed_at ), true ); ?>
      </span>
    </div>
    <!-- /.col-left -->
    <div class="col-right">
      <h3 class="status"><?php echo lang( 'paid' ); ?></h3>
    </div>
    <!-- /.col-right -->
  </div>
  <!-- /.section-1 -->
  <div class="section-2">
    <div class="col-left">
      <strong class="break margin-bottom-2"><?php echo lang( 'issed_by' ); ?>:</strong>
      <span class="break margin-bottom-1"><?php echo html_escape( db_config( 'iv_company_name' ) ); ?></span>
      <span class="break margin-bottom-1"><?php echo html_escape( db_config( 'iv_address_1' ) ); ?></span>
      <span class="break margin-bottom-1"><?php echo html_escape( db_config( 'iv_address_2' ) ); ?></span>
      <span class="break"><?php echo lang( 'tel' ); ?>: <?php echo html_escape( db_config( 'iv_phone_number' ) ); ?></span>
    </div>
    <!-- /.col-left -->
    <div class="col-right">
      <strong class="break margin-bottom-2"><?php echo lang( 'issed_to' ); ?>:</strong>
      <span><?php echo html_escape( $issued_to ); ?></span>
    </div>
    <!-- /.col-right -->
  </div>
  <!-- /.section-2 -->
  <div class="section-3">
    <div class="header">
      <div class="col-left">
        <strong><?php echo lang( 'payment_gateway' ); ?></strong>
      </div>
      <!-- /.col-left -->
      <div class="col-right">
        <strong><?php echo lang( 'transaction_id' ); ?></strong>
      </div>
      <!-- /.col-right -->
    </div>
    <!-- /.header -->
    <div class="body">
      <div class="col-left">
        <span><?php echo lang( html_escape( $gateway ) ); ?></span>
      </div>
      <!-- /.col-left -->
      <div class="col-right">
        <span>
          <?php
          if ( ! empty( $transaction_id ) )
          {
              echo str_replace( 'txn_', '', html_escape( $transaction_id ) );
          }
          else
          {
              echo lang( 'n_a' );
          }
          ?>
        </span>
      </div>
      <!-- /.col-right -->
    </div>
    <!-- /.body -->
  </div>
  <!-- /.section-3 -->
  <div class="section-4">
    <div class="header">
      <div class="col-left width-1">
        <strong><?php echo lang( 'hash_symbol' ); ?></strong>
      </div>
      <!-- /.col-left -->
      <div class="col-left">
        <strong><?php echo lang( 'item' ); ?></strong>
      </div>
      <!-- /.col-left -->
      <div class="col-right">
        <strong><?php echo lang( 'amount' ); ?></strong>
      </div>
      <!-- /.col-right -->
      <div class="col-right width-2">
        <strong><?php echo lang( 'quantity' ); ?></strong>
      </div>
      <!-- /.col-right -->
    </div>
    <!-- /.header -->
    <div class="body">
      <div class="col-left width-1">
        <span>1</span>
      </div>
      <!-- /.col-left -->
      <div class="col-left">
        <span><?php echo html_escape( $item_name ); ?></span>
      </div>
      <!-- /.col-left -->
      <div class="col-right width-2 text-right">
        <span><?php echo html_escape( $amount . ' ' . $code ); ?></span>
      </div>
      <!-- /.col-right -->
      <div class="col-right text-right">
        <span><?php echo html_escape( $quantity ); ?></span>
      </div>
      <!-- /.col-right -->
    </div>
    <!-- /.body -->
  </div>
  <!-- /.section-4 -->
  <div class="section-5">
   <div class="col-right text-right">
      <strong>
        <?php echo lang( 'total_amount' ); ?>:
        <?php echo html_escape( $amount . ' ' . $code ); ?>
      </strong>
    </div>
    <!-- /.col-right -->
  </div>
  <!-- /.section-5 -->
</div>
<!-- /.main-wrapper -->

</body>

</html>