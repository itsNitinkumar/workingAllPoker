<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h3 class="card-title"><?php echo lang( 'user_details' ); ?></h3>
    
    <?php if ( $this->uri->segment( 1 ) === 'admin' ) { ?>
      <div class="card-tools text-sm ml-auto">
        <button type="button" class="btn btn-sm btn-primary" data-toggle="dropdown">
          <span class="fas fa-ellipsis-v"></span>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item" href="<?php echo env_url( 'admin/users/sent_emails/' . html_escape( $user->username ) ); ?>">
            <i class="fas fa-share dropdown-menu-icon"></i> <?php echo lang( 'sent_emails' ); ?>
          </a>
          <a class="dropdown-item" href="<?php echo env_url( 'admin/users/activities_log/' . html_escape( $user->username ) ); ?>">
            <i class="fas fa-mouse dropdown-menu-icon"></i> <?php echo lang( 'activities_log' ); ?>
          </a>
          <a class="dropdown-item" href="<?php echo env_url( 'admin/users/sessions/' . html_escape( $user->username ) ); ?>">
            <i class="fab fa-firefox-browser dropdown-menu-icon"></i> <?php echo lang( 'sessions' ); ?>
          </a>
          <?php if ( $this->zuser->has_permission( 'payment' ) ) { ?>
            <a class="dropdown-item" href="<?php echo env_url( "admin/users/adjust_balance/{$user->id}" ); ?>">
              <i class="fas fa-wallet dropdown-menu-icon"></i> <?php echo lang( 'adjust_balance' ); ?>
            </a>
          <?php } ?>
        </div>
        <!-- /.dropdown-menu -->
      </div>
      <!-- /.card-tools -->
    <?php } ?>
  </div>
  <!-- /.card-header -->
  <div class="card-body c-fields">
    <div class="form-row">
      <div class="form-group col-md-6">
        <label for="first-name"><?php echo lang( 'first_name' ); ?> <span class="required">*</span></label>
        <input type="text" id="first-name" class="form-control" name="first_name" value="<?php echo html_escape( $user->first_name ); ?>" required>
      </div>
      <!-- /.form-group -->
      <div class="form-group col-md-6">
        <label for="last-name"><?php echo lang( 'last_name' ); ?> <span class="required">*</span></label>
        <input type="text" id="last-name" class="form-control" name="last_name" value="<?php echo html_escape( $user->last_name ); ?>" required>
      </div>
      <!-- /.form-group -->
    </div>
    <!-- /.form-row -->
    <div class="form-row">
      <div class="form-group col-xl-6">
        <label for="email-address">
          <?php echo lang( 'email_address' ); ?> <span class="required">*</span>
        </label>
        <input type="email" id="email-address" class="form-control" name="email_address" value="<?php echo html_escape( $user->email_address ); ?>" required <?php echo ( db_config( 'u_allow_email_change' ) == 0 ) ? 'readonly' : ''; ?>>
      </div>
      <!-- /.form-group -->
      <div class="form-group col-xl-6">
        <label for="username"><?php echo lang( 'username' ); ?> <span class="required">*</span></label>
        <input type="text" id="username" class="form-control" name="username" value="<?php echo html_escape( $user->username ); ?>" required <?php echo ( db_config( 'u_allow_username_change' ) == 0 ) ? 'readonly' : ''; ?>>
      </div>
      <!-- /.form-group -->
    </div>
    <!-- /.form-row -->
    <div class="form-group">
      <label for="about"><?php echo lang( 'about' ); ?></label>
      <textarea id="about" class="form-control" name="about" rows="5"><?php echo html_escape( $user->about ); ?></textarea>
    </div>
    <!-- /.form-group -->
    <div class="form-group">
      <label for="language"><?php echo lang( 'language' ); ?></label>
      <select class="form-control select2 search-disabled" id="language" name="language">
        <?php foreach ( AVAILABLE_LANGUAGES as $key => $value ) { ?>
          <option value="<?php echo html_escape( $key ); ?>" <?php echo select_single( $key, $user->language ); ?>><?php echo html_escape( $value['display_label'] ); ?></option>
        <?php } ?>
      </select>
    </div>
    <!-- /.form-group -->
    <div class="form-group">
      <label for="country"><?php echo lang( 'country' ); ?></label>
      <select class="form-control select2" id="country" data-placeholder="<?php echo lang( 'select_country' ); ?>" name="country">
        <option></option>
        
        <?php foreach ( get_countries() as $country ) { ?>
          <option value="<?php echo html_escape( $country->id ); ?>" <?php echo select_single( $country->id, $user->country_id ); ?>>
            <?php echo html_escape( $country->name ); ?>
          </option>
        <?php } ?>
      </select>
    </div>
    <!-- /.form-group -->
    <div class="form-group">
      <label for="currency"><?php echo lang( 'currency' ); ?></label>
      <select class="form-control select2" id="currency" data-placeholder="<?php echo lang( 'select_currency' ); ?>" name="currency">
        <option></option>
        
        <?php foreach ( get_currencies() as $currency ) { ?>
          <option value="<?php echo html_escape( $currency->id ); ?>" <?php echo select_single( $currency->id, $user->currency_id ); ?>>
            <?php echo html_escape( $currency->code . ' - ' . $currency->name ); ?>
          </option>
        <?php } ?>
      </select>
      </div>
    <!-- /.form-group -->
    <div class="form-group">
      <label for="gender"><?php echo lang( 'gender' ); ?></label>
      <select class="form-control select2 search-disabled" id="gender" data-placeholder="<?php echo lang( 'select_gender' ); ?>" name="gender">
        <option></option>
        <option value="female" <?php echo select_single( 'female', $user->gender ); ?>><?php echo lang( 'female' ); ?></option>
        <option value="male" <?php echo select_single( 'male', $user->gender ); ?>><?php echo lang( 'male' ); ?></option>
      </select>
    </div>
    <!-- /.form-group -->

    <div class="form-group">
      <label for="address-1"><?php echo lang( 'address_line_1' ); ?></label>
      <input type="text" id="address-1" class="form-control" name="address_1" value="<?php echo html_escape( $user->address_1 ); ?>">
    </div>
    <!-- /.form-group -->
    <div class="form-group">
      <label for="address-2"><?php echo lang( 'address_line_2' ); ?></label>
      <input type="text" id="address-2" class="form-control" name="address_2" value="<?php echo html_escape( $user->address_2 ); ?>">
    </div>
    <!-- /.form-group -->
    <div class="form-group">
      <label for="phone-number"><?php echo lang( 'phone_number' ); ?></label>
      <input type="text" id="phone-number" class="form-control" name="phone_number" value="<?php echo html_escape( $user->phone_number ); ?>">
    </div>
    <!-- /.form-group -->
    <div class="form-group">
      <label for="company"><?php echo lang( 'company' ); ?></label>
      <input type="text" id="company" class="form-control" name="company" value="<?php echo html_escape( $user->company ); ?>">
    </div>
    <!-- /.form-group -->
    <div class="form-group">
      <label for="state"><?php echo lang( 'state' ); ?></label>
      <input type="text" id="state" class="form-control" name="state" value="<?php echo html_escape( $user->state ); ?>">
    </div>
    <!-- /.form-group -->
    <div class="form-group">
      <label for="city"><?php echo lang( 'city' ); ?></label>
      <input type="text" id="city" class="form-control" name="city" value="<?php echo html_escape( $user->city ); ?>">
    </div>
    <!-- /.form-group -->
    <div class="form-group">
      <label for="zip-code"><?php echo lang( 'zip_code' ); ?></label>
      <input type="text" id="zip-code" class="form-control" name="zip_code" value="<?php echo html_escape( $user->zip_code ); ?>">
    </div>
    <!-- /.form-group -->
    <div class="form-group">
      <label for="time-format"><?php echo lang( 'time_format' ); ?></label>
      <select id="time-format" class="form-control select2 search-disabled" name="time_format">
        <option value="H:i:s" <?php echo select_single( 'H:i:s', $user->time_format ); ?>><?php echo lang( 'hours_24' ); ?></option>
        <option value="h:i:s A" <?php echo select_single( 'h:i:s A', $user->time_format ); ?>><?php echo lang( 'hours_12' ); ?></option>
      </select>
    </div>
    <!-- /.form-group -->
    <div class="form-group">
      <label for="date_format"><?php echo lang( 'date_format' ); ?></label>
      <select id="date_format" class="form-control select2 search-disabled" name="date_format">
        <option value="Y-m-d" <?php echo select_single( 'Y-m-d', $user->date_format ); ?>><?php echo lang( 'date_format_1' ); ?></option>
        <option value="Y/m/d" <?php echo select_single( 'Y/m/d', $user->date_format ); ?>><?php echo lang( 'date_format_2' ); ?></option>
        <option value="m-d-Y" <?php echo select_single( 'm-d-Y', $user->date_format ); ?>><?php echo lang( 'date_format_3' ); ?></option>
        <option value="m/d/Y" <?php echo select_single( 'm/d/Y', $user->date_format ); ?>><?php echo lang( 'date_format_4' ); ?></option>
        <option value="d-m-Y" <?php echo select_single( 'd-m-Y', $user->date_format ); ?>><?php echo lang( 'date_format_5' ); ?></option>
        <option value="d/m/Y" <?php echo select_single( 'd/m/Y', $user->date_format ); ?>><?php echo lang( 'date_format_6' ); ?></option>
      </select>
    </div>
    <!-- /.form-group -->
    <div class="form-group">
      <label for="timezone"><?php echo lang( 'timezone' ); ?></label>
      <select id="timezone" class="form-control select2" data-placeholder="<?php echo lang( 'select_timezone' ); ?>" name="timezone">
        <option></option>
        
        <?php foreach ( DateTimeZone::listIdentifiers( DateTimeZone::ALL ) as $timezone ) { ?>
          <option value="<?php echo html_escape( $timezone ); ?>" <?php echo select_single( $timezone, $user->timezone ); ?>><?php echo html_escape( $timezone ); ?></option>
        <?php } ?>
      </select>
    </div>
    <!-- /.form-group -->

    <?php

    if ( ! empty( $fields ) ) {
      foreach ( $fields as $field ) {
        $required = ( $field->is_required ) ? 'required' : '';
        $id = $field->id; ?>
      
      <div class="form-group">
        <label for="cf-<?php echo html_escape( $id ); ?>">
          <?php echo html_escape( $field->name ); ?>
          <?php echo ( $field->is_required ) ? '<span class="required">*</span>' : ''; ?>
        </label>
        
        <?php if ( $field->type === 'text' || $field->type === 'password' || $field->type === 'email' ) { ?>
          <input
            type="<?php echo html_escape( $field->type ); ?>"
            id="cf-<?php echo html_escape( $id ); ?>"
            class="form-control"
            name="cf_<?php echo html_escape( $id ); ?>"
            value="<?php echo html_escape( $field->value ); ?>"
            <?php echo html_escape( $required ); ?>>
        <?php } else if ( $field->type === 'textarea' ) { ?>
          <textarea
            id="cf-<?php echo html_escape( $id ); ?>"
            class="form-control"
            name="cf_<?php echo html_escape( $id ); ?>"
            rows="6"
            <?php echo html_escape( $required ); ?>><?php echo html_escape( $field->value ); ?></textarea>
        <?php } else if ( $field->type === 'checkbox' || $field->type === 'radio' ) {
          $options = explode( ',', $field->options );
          if ( count( $options ) > 0 ) {
            foreach ( $options as $key => $option ) {
              $option = trim( $option ); ?>
            <div class="icheck icheck-primary">
              <?php if ( $field->type === 'checkbox' ) { ?>
                <input
                  type="checkbox"
                  id="cf_<?php echo html_escape( $id ); ?>_<?php echo html_escape( $key ); ?>"
                  name="cf_<?php echo html_escape( $id ); ?>_<?php echo html_escape( $key ); ?>"
                  value="1"
                  <?php echo check_single_by_array( $option, explode( ',', $field->value ) ); ?>>
              <?php } else { ?>
                <input
                  type="radio"
                  id="cf_<?php echo html_escape( $id ); ?>_<?php echo html_escape( $key ); ?>"
                  name="cf_<?php echo html_escape( $id ); ?>"
                  value="<?php echo html_escape( $key ); ?>"
                  <?php echo html_escape( $required ); ?>
                  <?php echo check_single( $option, $field->value ); ?>>
              <?php } ?>
              <label for="cf_<?php echo html_escape( $id ); ?>_<?php echo html_escape( $key ); ?>">
                <?php echo html_escape( $option ); ?>
              </label>
            </div>
            <!-- /.icheck -->
          <?php }
          }
        } else if ( $field->type === 'select' ) {
          $options = explode( ',', $field->options );
          if ( count( $options ) > 0 ) { ?>
            <select
              class="form-control select2 search-disabled"
              id="cf-<?php echo html_escape( $id ); ?>"
              data-placeholder="<?php echo html_escape( $field->name ); ?>"
              name="cf_<?php echo html_escape( $id ); ?>"
              <?php echo html_escape( $required ); ?>>
              <option></option>
              
              <?php foreach ( $options as $key => $option ) {
                $option = trim( $option ); ?>
                <option value="<?php echo html_escape( $key ); ?>" <?php echo select_single( $option, $field->value ); ?>><?php echo html_escape( $option ); ?></option>
              <?php } ?>
            </select>
        <?php }
        } ?>
        
        <?php if ( ! empty( $field->guide_text ) ) { ?>
          <small class="form-text text-muted"><?php echo html_escape( $field->guide_text ); ?></small>
        <?php } ?>
        
      </div>
      <!-- /.from-group -->
      
    <?php }
    }?>
  </div>
  <!-- /.card-body -->
</div>
<!-- /.card -->