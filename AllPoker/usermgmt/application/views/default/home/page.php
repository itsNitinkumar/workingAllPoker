<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="container">
  <div class="row">
    <div class="col">
      <div class="pt-5 pb-4 border-bottom page">
        
        <?php if ( isset( $page->visibility ) ) {
          if ( $page->visibility == 0 ) { ?>
          <div class="alert alert-danger mb-4">
            <i class="fas fa-info-circle mr-1"></i> <?php echo lang( 'post_hidden' ); ?>
          </div>
        <?php } } ?>
        
        <h1><?php echo html_escape( $page->name ); ?></h1>
        <p><?php echo strip_extra_html( do_secure( $page->content, true ) ); ?></p>
      </div>
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->
</div>
<!-- /.container -->