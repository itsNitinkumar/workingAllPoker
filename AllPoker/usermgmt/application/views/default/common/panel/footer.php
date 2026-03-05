<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php if (! is_public_page()) { ?>
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <strong><?php printf(lang('copyright_main'), date('Y')); ?></strong>
    <?php echo lang('rights_reserved'); ?>
    <div class="float-right d-none d-sm-inline-block">
      <?php printf(lang('site_version'), ZUM_VERSION); ?>
    </div>
  </footer>
  </div>
  <!-- /.wrapper -->
<?php } ?>

<?php if (is_public_page() && db_config('site_show_cookie_popup')) { ?>
  <div class="cookie-popup card">
    <div class="card-body">
      <p><?php echo lang('cookie_message'); ?></p>
      <button class="mt-3 btn btn-block btn-primary text-sm accept-btn">
        <i class="fas fa-check-circle mr-2"></i> <?php echo lang('got_it'); ?>
      </button>
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.cookie-popup -->
<?php } ?>

<?php if (! empty(db_config('google_analytics_id'))) { ?>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo html_escape(db_config('google_analytics_id')); ?>"></script>
<?php } ?>

<?php if (db_config('captcha_plugin') == 'google_recaptcha' && is_gr_togo() && !empty($captcha_field)) { ?>
  <!-- Google reCaptcha: -->
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php } else if (db_config('captcha_plugin') == 'cloudflare_turnstile' && is_cloudflare_turnstile_togo() && !empty($captcha_field)) { ?>
  <!-- Cloudflare Turnstile: -->
  <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" defer></script>
<?php } else if (db_config('captcha_plugin') == 'hcaptcha' && is_hcaptcha_togo() && !empty($captcha_field)) { ?>
  <!-- hCaptcha: -->
  <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
<?php } ?>

<!-- Pace: -->
<script src="<?php assets_path('vendor/pace/pace.js'); ?>"></script>

<!-- Overlay Scrollbars: -->
<script src="<?php admin_lte_asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js'); ?>"></script>

<!-- jQuery Upload Preview: -->
<script src="<?php assets_path('panel/vendor/jquery_upload_preview/js/jquery.uploadPreview.min.js'); ?>"></script>

<!-- jQuery Cookie: -->
<script src="<?php assets_path('vendor/jquery-cookie/jquery.cookie.js'); ?>"></script>

<!-- Bootstrap 4: -->
<script src="<?php admin_lte_asset('plugins/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>

<!-- Select 2: -->
<script src="<?php admin_lte_asset('plugins/select2/js/select2.full.min.js'); ?>"></script>

<!-- Summernote: -->
<script src="<?php admin_lte_asset('plugins/summernote/summernote-bs4.min.js'); ?>"></script>

<!-- AdminLTE App: -->
<script src="<?php admin_lte_asset('js/adminlte.min.js'); ?>"></script>

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
<script src="<?php assets_path('panel/js/script.js?v=' . v_combine()); ?>"></script>
<script src="<?php assets_path('js/script.js?v=' . v_combine()); ?>"></script>

<?php if (db_config('custom_js') != '') { ?>
  <script>
    <?php echo db_config('custom_js'); ?>
  </script>
<?php } ?>

</body>

</html>