<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-12">
        <form class="z-form" action="<?php admin_action('settings/general'); ?>" method="post" enctype="multipart/form-data" data-csrf="manual">
          <div class="response-message"><?php echo alert_message(); ?></div>
          <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
          <div class="card card-primary card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
              <ul class="nav nav-tabs" role="tablist">
                <li class="pt-2 px-3"><?php echo lang('general_settings'); ?></li>
                <li class="nav-item">
                  <a class="nav-link active" id="basic-tab" data-toggle="pill" href="#basic" role="tab" aria-controls="basic" aria-selected="true">
                    <?php echo lang('basic'); ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="logo-and-favicon-tab" data-toggle="pill" href="#logo-and-favicon" role="tab" aria-controls="logo-and-favicon" aria-selected="false">
                    <?php echo lang('logo_and_favicon'); ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="maintenance-tab" data-toggle="pill" href="#maintenance" role="tab" aria-controls="maintenance" aria-selected="false">
                    <?php echo lang('maintenance'); ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="miscellaneous-tab" data-toggle="pill" href="#miscellaneous" role="tab" aria-controls="miscellaneous" aria-selected="false">
                    <?php echo lang('miscellaneous'); ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="social-links-tab" data-toggle="pill" href="#social-links" role="tab" aria-controls="social-links" aria-selected="false">
                    <?php echo lang('social_links'); ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="restful-api-tab" data-toggle="pill" href="#restful-api" role="tab" aria-controls="restful-api" aria-selected="false">
                    <?php echo lang('restful_api'); ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="custom-css-tab" data-toggle="pill" href="#custom-css" role="tab" aria-controls="custom-css" aria-selected="false">
                    <?php echo lang('custom_css'); ?>
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="custom-js-tab" data-toggle="pill" href="#custom-js" role="tab" aria-controls="custom-js" aria-selected="false">
                    <?php echo lang('custom_js'); ?>
                  </a>
                </li>
              </ul>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="tab-content">

                <!-- Basic: -->
                <div class="tab-pane show active" id="basic" role="tabpanel" aria-labelledby="basic-tab">
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="site-name"><?php echo lang('site_name'); ?> <span class="required">*</span></label>
                      <input type="text" id="site-name" class="form-control" name="site_name" value="<?php echo html_escape(db_config('site_name')); ?>">
                    </div>
                    <!-- /.form-group -->
                    <div class="form-group col-md-6">
                      <label for="site-tagline"><?php echo lang('site_tagline'); ?> <span class="required">*</span></label>
                      <input type="text" id="site-tagline" class="form-control" name="site_tagline" value="<?php echo html_escape(db_config('site_tagline')); ?>">
                    </div>
                    <!-- /.form-group -->
                  </div>
                  <!-- /.form-row -->
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="site-theme"><?php echo lang('site_theme'); ?></label>
                      <select class="form-control select2 search-disabled" id="site-theme" name="site_theme">
                        <?php foreach (SITE_THEMES as $key => $value) { ?>
                          <option value="<?php echo html_escape($key); ?>" <?php echo select_single($key, db_config('site_theme')); ?>><?php echo html_escape($value['display_label']); ?></option>
                        <?php } ?>
                      </select>
                    </div>
                    <!-- /.form-group -->
                    <div class="form-group col-md-6">
                      <label for="captcha-plugin"><?php echo lang('captcha_plugin'); ?></label>
                      <select class="form-control select2 search-disabled" id="captcha-plugin" name="captcha_plugin">
                        <option value="google_recaptcha" <?php echo select_single('google_recaptcha', db_config('captcha_plugin')); ?>><?php echo lang('google_recaptcha'); ?></option>
                        <option value="cloudflare_turnstile" <?php echo select_single('cloudflare_turnstile', db_config('captcha_plugin')); ?>><?php echo lang('cloudflare_turnstile'); ?></option>
                        <option value="hcaptcha" <?php echo select_single('hcaptcha', db_config('captcha_plugin')); ?>><?php echo lang('hcaptcha'); ?></option>
                      </select>
                    </div>
                    <!-- /.form-group -->
                  </div>
                  <!-- /.form-row -->
                  <div class="form-group">
                    <label for="site-timezone"><?php echo lang('timezone'); ?></label>
                    <select id="site-timezone" class="form-control select2" data-placeholder="<?php echo lang('select_timezone'); ?>" name="site_timezone">
                      <option></option>

                      <?php foreach (DateTimeZone::listIdentifiers(DateTimeZone::ALL) as $timezone) { ?>
                        <option value="<?php echo html_escape($timezone); ?>" <?php echo select_single($timezone, db_config('site_timezone')); ?>><?php echo html_escape($timezone); ?></option>
                      <?php } ?>
                    </select>
                  </div>
                  <!-- /.form-group -->
                  <div class="form-group">
                    <label for="site-about"><?php echo lang('site_about'); ?></label>
                    <textarea id="site-about" class="form-control" name="site_about" rows="5"><?php echo html_escape(db_config('site_about')); ?></textarea>
                  </div>
                  <!-- /.form-group -->
                  <div class="form-group">
                    <label for="site-description"><?php echo lang('site_description'); ?></label>
                    <textarea id="site-description" class="form-control" name="site_description" rows="5"><?php echo html_escape(db_config('site_description')); ?></textarea>
                  </div>
                  <!-- /.form-group -->
                  <label for="site-keywords"><?php echo lang('site_keywords'); ?></label>
                  <input type="text" id="site-keywords" class="form-control" name="site_keywords" value="<?php echo html_escape(db_config('site_keywords')); ?>">
                </div>
                <!-- /.tab-pane -->

                <!-- Logo and Favicon: -->
                <div class="tab-pane" id="logo-and-favicon" role="tabpanel" aria-labelledby="logo-and-favicon-tab">
                  <div class="form-group position-relative">
                    <label for="site-logo" class="d-block"><?php echo lang('site_logo'); ?></label>

                    <?php if (! empty(db_config('site_logo'))) { ?>
                      <button type="button" class="btn btn-danger text-sm tr-absolute" data-toggle="modal" data-target="#delete-site-logo">
                        <i class="fas fa-trash mr-2"></i> <?php echo lang('delete_logo'); ?>
                      </button>
                      <img class="d-block mb-2 settings-logo-view" src="<?php echo general_uploads(html_escape(db_config('site_logo'))); ?>" alt="">
                    <?php } ?>

                    <input type="file" id="site-logo" name="site_logo" accept="<?php echo ALLOWED_IMG_EXT_HTML; ?>">
                  </div>
                  <!-- /.form-group -->
                  <label for="site-favicon" class="d-block"><?php echo lang('site_favicon'); ?></label>

                  <?php if (! empty(db_config('site_favicon'))) { ?>
                    <img class="d-block mb-2 favicon-lg" src="<?php echo general_uploads(html_escape(db_config('site_favicon'))); ?>" alt="">
                  <?php } ?>

                  <input type="file" id="site-favicon" name="site_favicon" accept="<?php echo ALLOWED_IMG_EXT_HTML; ?>">
                </div>
                <!-- /.tab-pane -->

                <!-- Maintenance: -->
                <div class="tab-pane" id="maintenance" role="tabpanel" aria-labelledby="maintenance-tab">
                  <div class="form-group">
                    <label for="allowed-ips"><?php echo lang('allowed_ips'); ?></label>
                    <textarea id="allowed-ips" class="form-control" name="mm_allowed_ips" rows="5"><?php echo html_escape(db_config('mm_allowed_ips')); ?></textarea>
                    <small class="form-text text-muted"><?php echo lang('mm_ip_addr_tip'); ?></small>
                  </div>
                  <!-- /.form-group -->
                  <div class="form-group">
                    <label for="message"><?php echo lang('leave_visitors_msg'); ?> <span class="required">*</span></label>
                    <textarea id="message" class="form-control" name="mm_message" rows="5"><?php echo html_escape(db_config('mm_message')); ?></textarea>
                  </div>
                  <!-- /.form-group -->
                  <label class="d-block"><?php echo lang('maintenance_mode'); ?></label>
                  <div class="icheck icheck-primary d-inline-block mr-2">
                    <input type="radio" name="maintenance_mode" id="maintenance-mode-1" value="1" <?php echo check_single(1, db_config('maintenance_mode')); ?>>
                    <label for="maintenance-mode-1"><?php echo lang('enable'); ?></label>
                  </div>
                  <!-- /.icheck -->
                  <div class="icheck icheck-primary d-inline-block">
                    <input type="radio" name="maintenance_mode" id="maintenance-mode-0" value="0" <?php echo check_single(0, db_config('maintenance_mode')); ?>>
                    <label for="maintenance-mode-0"><?php echo lang('disable'); ?></label>
                  </div>
                  <!-- /.icheck -->
                </div>
                <!-- /.tab-pane -->

                <!-- Miscellaneous: -->
                <div class="tab-pane" id="miscellaneous" role="tabpanel" aria-labelledby="miscellaneous-tab">
                  <div class="form-group">
                    <label for="dashboard-cache-time"><?php echo lang('dashboard_cache_time'); ?></label>
                    <input type="number" id="dashboard-cache-time" class="form-control" name="dashboard_cache_time" value="<?php echo html_escape(db_config('dashboard_cache_time')); ?>">
                    <small class="form-text text-muted"><?php echo lang('d_cache_tip'); ?></small>
                  </div>
                  <!-- /.form-group -->
                  <div class="form-group">
                    <label class="d-block"><?php echo lang('show_cookie_popup'); ?></label>
                    <div class="icheck icheck-primary d-inline-block mr-2">
                      <input type="radio" name="site_show_cookie_popup" id="site-show-cookie-popup-1" value="1" <?php echo check_single(1, db_config('site_show_cookie_popup')); ?>>
                      <label for="site-show-cookie-popup-1"><?php echo lang('yes'); ?></label>
                    </div>
                    <!-- /.icheck -->
                    <div class="icheck icheck-primary d-inline-block">
                      <input type="radio" name="site_show_cookie_popup" id="site-show-cookie-popup-0" value="0" <?php echo check_single(0, db_config('site_show_cookie_popup')); ?>>
                      <label for="site-show-cookie-popup-0"><?php echo lang('no'); ?></label>
                    </div>
                    <!-- /.icheck -->
                  </div>
                  <!-- /.form-group -->
                  <label class="d-block"><?php echo lang('enable_newsletter'); ?></label>
                  <div class="icheck icheck-primary d-inline-block mr-2">
                    <input type="radio" name="enable_newsletter" id="enable-newsletter-1" value="1" <?php echo check_single(1, db_config('enable_newsletter')); ?>>
                    <label for="enable-newsletter-1"><?php echo lang('yes'); ?></label>
                  </div>
                  <!-- /.icheck -->
                  <div class="icheck icheck-primary d-inline-block">
                    <input type="radio" name="enable_newsletter" id="enable-newsletter-0" value="0" <?php echo check_single(0, db_config('enable_newsletter')); ?>>
                    <label for="enable-newsletter-0"><?php echo lang('no'); ?></label>
                  </div>
                  <!-- /.icheck -->
                </div>
                <!-- /.tab-pane -->

                <!-- Social Links: -->
                <div class="tab-pane" id="social-links" role="tabpanel" aria-labelledby="social-links-tab">
                  <div class="form-group">
                    <label for="facebook"><?php echo lang('facebook'); ?></label>
                    <input type="url" id="facebook" class="form-control" name="facebook_link" value="<?php echo html_esc_url(db_config('facebook_link')); ?>">
                  </div>
                  <!-- /.form-group -->
                  <div class="form-group">
                    <label for="twitter"><?php echo lang('twitter'); ?></label>
                    <input type="url" id="twitter" class="form-control" name="twitter_link" value="<?php echo html_esc_url(db_config('twitter_link')); ?>">
                  </div>
                  <!-- /.form-group -->
                  <div class="form-group">
                    <label for="linkedin"><?php echo lang('linkedin'); ?></label>
                    <input type="url" id="linkedin" class="form-control" name="linkedin_link" value="<?php echo html_esc_url(db_config('linkedin_link')); ?>">
                  </div>
                  <!-- /.form-group -->
                  <label for="youtube"><?php echo lang('youtube'); ?></label>
                  <input type="url" id="youtube" class="form-control" name="youtube_link" value="<?php echo html_esc_url(db_config('youtube_link')); ?>">
                </div>
                <!-- /.tab-pane -->

                <!-- RESTful API: -->
                <div class="tab-pane" id="restful-api" role="tabpanel" aria-labelledby="restful-api-tab">
                  <label class="d-block"><?php echo lang('enable_restful_api'); ?></label>
                  <div class="icheck icheck-primary d-inline-block mr-2">
                    <input type="radio" name="enable_restful_api" id="enable-restful-api-1" value="1" <?php echo check_single(1, db_config('enable_restful_api')); ?>>
                    <label for="enable-restful-api-1"><?php echo lang('yes'); ?></label>
                  </div>
                  <!-- /.icheck -->
                  <div class="icheck icheck-primary d-inline-block">
                    <input type="radio" name="enable_restful_api" id="enable-restful-api-0" value="0" <?php echo check_single(0, db_config('enable_restful_api')); ?>>
                    <label for="enable-restful-api-0"><?php echo lang('no'); ?></label>
                  </div>
                  <!-- /.icheck -->
                </div>
                <!-- /.tab-pane -->

                <!-- Custom CSS: -->
                <div class="tab-pane" id="custom-css" role="tabpanel" aria-labelledby="custom-css-tab">
                  <textarea class="form-control" name="custom_css" rows="5"><?php echo html_escape(db_config('custom_css')); ?></textarea>
                  <small class="form-text text-muted"><?php echo lang('custom_css_tip'); ?></small>
                </div>
                <!-- /.tab-pane -->

                <!-- Custom JS: -->
                <div class="tab-pane" id="custom-js" role="tabpanel" aria-labelledby="custom-js-tab">
                  <textarea class="form-control" name="custom_js" rows="5"><?php echo html_escape(db_config('custom_js')); ?></textarea>
                  <small class="form-text text-muted"><?php echo lang('custom_js_tip'); ?></small>
                </div>
                <!-- /.tab-pane -->
              </div>
              <!-- /.tab-content -->
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <button type="submit" class="btn btn-primary float-right text-sm">
                <i class="fas fa-check-circle mr-2"></i> <?php echo lang('update'); ?>
              </button>
            </div>
            <!-- /.card-footer -->
          </div>
          <!-- /.card -->
        </form>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container-fluid -->
</div>
<!-- /.content -->

<?php load_modals('admin/delete_site_logo'); ?>