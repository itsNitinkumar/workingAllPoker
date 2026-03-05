<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="social-auth-links text-center mb-3">
  <?php if ( is_fb_togo() || is_tw_togo() || is_gl_togo() || is_vkontakte_togo() ) { ?>
    <p>- <?php echo lang( 'OR' ); ?> -</p>
    
    <?php if ( is_fb_togo() ) { ?>
      <a href="<?php echo env_url( 'login/facebook' ); ?>" class="btn btn-block btn-primary text-left text-sm">
        <i class="fab fa-facebook mr-2"></i>
        <?php echo lang( 'continue_with_fb' ); ?>
      </a>
    <?php } ?>
    
    <?php if ( is_tw_togo() ) { ?>
      <a href="<?php echo env_url( 'login/twitter' ); ?>" class="btn btn-block btn-primary text-left text-sm">
        <i class="fab fa-twitter mr-2"></i>
        <?php echo lang( 'continue_with_twitter' ); ?>
      </a>
    <?php } ?>
    
    <?php if ( is_gl_togo() ) { ?>
      <a href="<?php echo env_url( 'login/google' ); ?>" class="btn btn-block btn-danger text-left text-sm">
        <i class="fab fa-google-plus mr-2"></i>
        <?php echo lang( 'continue_with_google' ); ?>
      </a>
    <?php } ?>

    <?php if ( is_vkontakte_togo() ) { ?>
      <a href="<?php echo env_url( 'login/vkontakte' ); ?>" class="btn btn-block btn-primary text-left text-sm">
        <i class="fab fa-vk mr-2"></i>
        <?php echo lang( 'continue_with_vkontakte' ); ?>
      </a>
    <?php } ?>
  <?php } ?>
</div>
<!-- /.social-auth-links -->