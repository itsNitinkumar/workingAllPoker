<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="login-box">
  <div class="login-logo">
    <span><?php echo lang( 'account_banned' ); ?></span>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <?php printf( lang( 'account_banned_msg' ), env_url( 'terms' ) ); ?>
    </div>
    <!-- /.login-card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->