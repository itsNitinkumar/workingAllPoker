<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="container">
  <div class="row">
    <div class="col">
      <div class="pt-5 pb-5">
        <div class="row">
          <div class="col-lg-4">
            <p class="mb-0">
              <strong><?php printf(lang('copyright_main'), date('Y')); ?></strong>
              <?php echo lang('rights_reserved'); ?>
            </p>
          </div>
          <!-- /.col -->
          <div class="col-lg-8">
            <ul class="footer-social pl-3 float-lg-right">
              <?php if (! empty(db_config('youtube_link'))) { ?>
                <li>
                  <a class="text-dark" href="<?php echo html_esc_url(db_config('youtube_link')); ?>" target="_blank">
                    <i class="fab fa-youtube"></i>
                  </a>
                </li>
              <?php } ?>

              <?php if (! empty(db_config('linkedin_link'))) { ?>
                <li>
                  <a class="text-dark" href="<?php echo html_esc_url(db_config('linkedin_link')); ?>" target="_blank">
                    <i class="fab fa-linkedin"></i>
                  </a>
                </li>
              <?php } ?>

              <?php if (! empty(db_config('twitter_link'))) { ?>
                <li>
                  <a class="text-dark" href="<?php echo html_esc_url(db_config('twitter_link')); ?>" target="_blank">
                    <i class="fab fa-twitter"></i>
                  </a>
                </li>
              <?php } ?>

              <?php if (! empty(db_config('facebook_link'))) { ?>
                <li>
                  <a class="text-dark" href="<?php echo html_esc_url(db_config('facebook_link')); ?>" target="_blank">
                    <i class="fab fa-facebook"></i>
                  </a>
                </li>
              <?php } ?>
            </ul>
            <ul class="footer-menu float-lg-right">
              <?php if (! empty(get_custom_pages())) {
                foreach (get_custom_pages() as $custom_page) { ?>
                  <li>
                    <a class="text-dark" href="<?php echo env_url('page/' . html_escape($custom_page->slug)); ?>"><?php echo html_escape($custom_page->name); ?></a>
                  </li>
              <?php }
              } ?>
              <li>
                <a class="text-dark" href="<?php echo env_url('privacy-policy'); ?>"><?php echo lang('privacy_policy'); ?></a>
              </li>
              <li>
                <a class="text-dark" href="<?php echo env_url('terms'); ?>"><?php echo lang('terms_of_use'); ?></a>
              </li>
              <li class="dropdown">
                <a href="#" class="text-primary ml-2" id="language-switch" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <?php echo get_language_label(get_language()); ?> <i class="fas fa-angle-down"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right text-sm" aria-labelledby="language-switch">
                  <?php foreach (AVAILABLE_LANGUAGES as $key => $value) { ?>

                    <?php if ($key !== get_language()) { ?>
                      <a href="<?php echo env_url(); ?>language/switch/<?php echo html_escape($key); ?>" class="dropdown-item no-hover">
                        <small><?php echo html_escape($value['display_label']); ?></small>
                      </a>
                    <?php } else { ?>
                      <small class="dropdown-item text-sm">
                        <?php echo html_escape($value['display_label']); ?>

                        <span class="float-right text-primary">
                          <i class="fas fa-check-circle mt-1"></i>
                        </span>
                      </small>
                    <?php } ?>

                  <?php } ?>
                </div>
                <!-- /.dropdown-menu -->
              </li>
            </ul>
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->
</div>
<!-- /.container -->

<?php if (db_config('captcha_plugin') == 'google_recaptcha' && is_gr_togo()) { ?>
  <!-- Google reCaptcha: -->
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php } else if (db_config('captcha_plugin') == 'cloudflare_turnstile' && is_cloudflare_turnstile_togo()) { ?>
  <!-- Cloudflare Turnstile: -->
  <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" defer></script>
<?php } else if (db_config('captcha_plugin') == 'hcaptcha' && is_hcaptcha_togo()) { ?>
  <!-- hCaptcha: -->
  <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
<?php } ?>

<?php if (db_config('custom_css') != '') { ?>
  <style>
    <?php echo html_escape(db_config('custom_css')); ?>
  </style>
<?php } ?>

<?php if (! empty($scripts))
  load_scripts($scripts);
?>

<!-- Custom Scripts: -->
<script src="<?php assets_path('js/functions.js?v=' . v_combine()); ?>"></script>
<script src="<?php assets_path('js/script.js?v=' . v_combine()); ?>"></script>

<?php if (db_config('custom_js') != '') { ?>
  <script>
    <?php echo db_config('custom_js'); ?>
  </script>
<?php } ?>

</body>

</html>