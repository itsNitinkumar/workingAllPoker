<?php if (db_config('captcha_plugin') == 'google_recaptcha' && is_gr_togo()) { ?>
  <div class="form-group <?php echo !isset($no_text_center) ? 'text-center' : ''; ?>">
    <div class="g-recaptcha" data-sitekey="<?php echo html_escape(db_config('gr_public_key')); ?>"></div>
  </div>
  <!-- /.form-group -->
<?php } else if (db_config('captcha_plugin') == 'cloudflare_turnstile') { ?>
  <div class="form-group <?php echo !isset($no_text_center) ? 'text-center' : ''; ?>">
    <div class="cf-turnstile" data-sitekey="<?php echo html_escape(db_config('cloudflare_turnstile_site_key')); ?>"></div>
  </div>
  <!-- /.form-group -->
<?php } else if (db_config('captcha_plugin') == 'hcaptcha') { ?>
  <div class="form-group <?php echo !isset($no_text_center) ? 'text-center' : ''; ?>">
    <div class="h-captcha d-inline-block" data-sitekey="<?php echo html_escape(db_config('hcaptcha_site_key')); ?>"></div>
  </div>
<?php } ?>