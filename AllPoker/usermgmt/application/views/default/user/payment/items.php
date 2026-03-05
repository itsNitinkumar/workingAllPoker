<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col">
        <div class="not-in-form">
          <div class="response-message"><?php echo alert_message(); ?></div>
        </div>
        <!-- /.not-in-form -->
        
        <?php if ( $this->zuser->get( 'premium_time' ) == -1 ) { ?>
          <div class="alert alert-info">
            <p><?php echo lang( 'unlimited_premium_time' ); ?></p>
          </div>
          <!-- /.alert -->
        <?php } else if ( $this->zuser->get( 'premium_time' ) > time() ) { ?>
          <div class="alert alert-info">
            <p><?php printf( lang( 'remaining_premium_time' ), remaining_time( $this->zuser->get( 'premium_time' ) ) ); ?></p>
          </div>
          <!-- /.alert -->
        <?php } ?>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
    <div class="row">
      
      <?php
      if ( ! empty( $items ) )
      {
        foreach ( $items as $item ) {
          $price = $item->price + 0 . ' ' . $item->code; ?>
        <div class="col-md-4">
          <div class="card z-card z-bg-card position-relative p-3">
            <div class="card-body">
              <div class="ribbon-wrapper ribbon-lg">
                <div class="ribbon bg-light">
                  <?php echo html_escape( $item->name ); ?>
                </div>
                <!-- /.ribbon -->
              </div>
              <!-- /.ribbon-wrapper -->
              
              <span><?php echo lang( html_escape( $item->type ) ); ?></span>
              <h1 class="font-weight-bold mb-0"><?php echo html_escape( $price ); ?></h1>
              
              <?php if ( $this->zuser->get( 'premium_item_id' ) == $item->id && $this->zuser->check_premium_time() ) { ?>
                <strong class="badge badge-primary"><?php echo lang( 'purchased' ); ?></strong>
              <?php } ?>
              
              <?php if ( ! empty( $item->days ) ) { ?>
                <span class="badge badge-danger"><?php echo html_escape( $item->days . ' ' . lang( 'days' ) ); ?></span>
              <?php } else if ( empty( $item->days ) && $item->type == 'purchase' ) { ?>
                <span class="badge badge-danger"><?php echo lang( 'unlimited_days' ); ?></span>
              <?php } ?>
              
              <p class="mt-3"><?php echo html_escape( $item->description ); ?></p>
              
              <div class="mt-3">
                <?php if ( is_stripe_togo() ) { ?>
                  <button
                    class="btn btn-primary btn-block tool pay-modal mb-2"
                    data-title="<?php echo html_escape( sprintf( lang( 'sp_modal_title' ), $price, $item->name ) ); ?>"
                    data-type="<?php echo lang( html_escape( $item->type ) ); ?>"
                    data-id="<?php echo html_escape( $item->id ); ?>"
                    data-price="<?php echo html_escape( $price ); ?>"
                    data-toggle="modal"
                    data-target="#pay-with-stripe"
                    disabled>
                    <i class="fab fa-cc-stripe mr-2 tool-c"></i>
                    <?php echo lang( 'pay_with_stripe' ); ?>
                  </button>
                <?php } ?>
                
                <?php if ( db_config( 'credit_pay_enable' ) && $item->type !== 'top_up' ) { ?>
                  <button
                    class="btn btn-secondary btn-block tool pay-modal pwc"
                    data-title="<?php echo html_escape( sprintf( lang( 'pwc_modal_title' ), $price, $item->name ) ); ?>"
                    data-type="<?php echo lang( html_escape( $item->type ) ); ?>"
                    data-id="<?php echo html_escape( $item->id ); ?>"
                    data-price="<?php echo html_escape( $price ); ?>"
                    data-toggle="modal"
                    data-target="#pay-with-credit"
                    disabled>
                    <i class="fas fa-wallet tool-c mr-2"></i>
                    <?php echo lang( 'pay_with_credit' ); ?>
                  </button>
                <?php } ?>
              </div>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      <?php }
      } else { ?>
        <div class="col">
          <div class="card">
            <div class="card-body">
              <h3 class="mb-0">
                <i class="fas fa-store-alt-slash mr-2"></i>
                <?php echo lang( 'no_items_to_purchase' ); ?>
              </h3>
              <?php if ( $this->zuser->has_permission( 'payment' ) ) { ?>
                <span class="text-muted d-block mt-3"><?php echo lang( 'add_item_tip' ); ?></span>
              <?php } ?>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      <?php } ?>
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container-fluid -->
</div>
<!-- /.content -->

<?php load_modals( ['user/pay_with_stripe', 'user/pay_with_credit'] ); ?>