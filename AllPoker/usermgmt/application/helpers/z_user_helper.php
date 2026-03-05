<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Login Helper
 *
 * @author Shahzaib
 */


/**
 * Send Welcome Email
 *
 * @param   integer $id
 * @return  string
 * @version 1.9
 */
if ( ! function_exists( 'send_welcome_email' ) )
{
    function send_welcome_email( $id )
    {
        if ( db_config( 'u_send_welcome_email' ) == 0 ) return 'disabled';
        
        $ci =& get_instance();
        
        $template = $ci->Tool_model->email_template_by_hook_and_lang( 'welcome_user', get_language() );
        
        if ( ! empty( $template ) )
        {
            $ci->load->model( 'User_model' );
            
            $user = $ci->User_model->get_by_id( $id );
            
            if ( ! empty( $user ) )
            {
                if ( ! is_email_settings_filled() ) return 'missing_email_config';
                
                $message = replace_placeholders( $template->template, [
                    '{USER_NAME}' => $user->first_name . ' ' . $user->last_name,
                    '{LOGIN_USERNAME}' => $user->username,
                    '{EMAIL_LINK}' => env_url( 'login' ),
                    '{SITE_NAME}' => db_config( 'site_name' )
                ]);
                
                $ci->load->library( 'ZMailer' );
                
                if ( ! $ci->zmailer->send_email( $user->email_address, $template->subject, $message ) )
                {
                    return 'failed_email';
                }
                
                return true;
            }
            
            return 'invalid_req';
        }
        
        return 'missing_template';
    }
}

/**
 * Cleaned Email Username
 *
 * @param   string $email
 * @return  string
 * @version 1.6
 */
if ( ! function_exists( 'cleaned_email_username' ) )
{
    function cleaned_email_username( $email )
    {
        $email = explode( '@', $email );
        $username = preg_replace( '/[^A-Za-z0-9_-]/', '', $email[0] );
        $length = strlen( $username );
        
        if ( $length < 5 )
        {
            $chars = 'abcdefghijklmnopqrstuvwxyz';
            $string = substr( str_shuffle( $chars ), 0, ( 5 - $length ) );
            $username .= $string;
        }
        
        return $username;
    }
}

/**
 * Delete User Profile Picture
 *
 * @param   integer $user_id
 * @return  boolean
 * @version 1.6
 */
if ( ! function_exists( 'delete_profile_picture' ) )
{
    function delete_profile_picture( $user_id )
    {
        $ci =& get_instance();
        
        $ci->load->model( 'User_model' );
        $ci->load->library( 'ZFiles' );
        
        $user = $ci->User_model->get_by_id( $user_id );
        
        if ( empty( $user ) )
        {
            return 'invalid_req';
        }
        
        // Check if the image is hosted on a third-party server
        // ( e.g. Facebook ), don't perform the deletion:
        if ( ! filter_var( $user->picture, FILTER_VALIDATE_URL ) )
        {
            if ( $user->picture !== DEFAULT_USER_IMG )
            {
                $ci->zfiles->delete_image_file( 'users', $user->picture );
            }
        }
        
        $status = $ci->User_model->update_user(
        [
            'picture' => DEFAULT_USER_IMG
        ], $user_id );
        
        return $status;
    }
}

/**
 * User Locally Locked Check
 *
 * @param  string  $value
 * @param  string  $type
 * @param  boolean $return
 * @return boolean
 */
if ( ! function_exists( 'user_locally_locked_check' ) )
{
    function user_locally_locked_check( $value, $type = 'login', $return = false )
    {
        $ci =& get_instance();
        $ci->load->model( 'Login_model' );
        $li = $ci->Login_model->invalid_attempts( $value, $type );
        $status = false;
        
        if ( ! empty( $li ) )
        {
            $unlock_time = subtract_time( get_lockout_unlock_time() );
            $max_attempts = get_max_allowed_attempts();
            
            // Check invalid attempts that are performed under the fifteen minutes:
            if ( $li->attempted_at > subtract_time( '15 minutes' ) && $li->is_locked == 0 )
            {
                // If the count is crossed the maximum allowed attempts,
                // lock the account locally ( for a specific IP ):
                if ( $li->count >= $max_attempts )
                {
                    $ci->Login_model->lock_user_locally( $li->id, $type );
                    $status = true;
                }
            }
            
            // If a attempt is performed after fifteen minutes of the
            // last attemp, clear the recent count:
            else if ( $li->is_locked == 0 )
            {
                $ci->Login_model->clear_attempts_count( $value, $type );
            }
            
            if ( $li->attempted_at > $unlock_time && $li->is_locked == 1 )
            {
                if ( $li->count >= $max_attempts )
                {
                    $status = true;
                }
            }
            
            // If lockout time of a locked account is crossed the selected
            // time, delete the attempt record:
            else if ( $li->is_locked == 1 )
            {
                $ci->Login_model->delete_invalid_attempt( $value, $type );
            }
        }
        
        if ( $status )
        {
            // If the status is true, display the error message with the
            // waiting time for the next try:
            $time_limit = get_lockout_unlock_time( true );
            $sec = $time_limit - intval( time() - $li->attempted_at );
            $time_format = ( db_config( 'u_lockout_unlock_time' ) == 4 ) ? 'H:i:s' : 'i:s';
            $rem_time = gmdate( $time_format, $sec );
            $locked_message = sprintf( err_lang( 'too_many_attempts' ), $rem_time );
            
            if ( $return === false ) d_r_error_c( $locked_message );
            else return $locked_message;
        }
    }
}

/**
 * Validate Password
 *
 * Use to validate the password based on setting.
 *
 * @param  string $pass
 * @return array
 */
if ( ! function_exists( 'validate_password' ) )
{
    function validate_password( $pass )
    {
        $req = db_config( 'u_password_requirement' );
        $status = false;
        $message = '';
        $regex = '';
        
        if ( $req === 'strong' )
        {
            $regex = '/^(?=.*[0-9])(?=.*[a-zA-z])(?=.*[.,:;?!~`\\\@#$%^&|[\[\](){}\/<>\"\'*_+=-]).{12,}$/';
            
            if ( ! preg_match( $regex, $pass ) )
            {
                $message = 'pwd_strong';
            }
            else
            {
                $status = true;
            }
        }
        else if ( $req === 'medium' )
        {
            $regex = '/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).{8,}$/';
            
            if ( ! preg_match( $regex, $pass ) )
            {
                $message = 'pwd_medium';
            }
            else
            {
                $status = true;
            }
        }
        else if ( $req === 'normal' )
        {
            $regex = '/^(?=.*[0-9])(?=.*[a-zA-z]).{6,}$/';
            
            if ( ! preg_match( $regex, $pass ) )
            {
                $message = 'pwd_normal';
            }
            else
            {
                $status = true;
            }
        }
        else if ( $req === 'low' )
        {
            if ( strlen( $pass ) < 6 )
            {
                $message = 'pwd_low';
            }
            else
            {
                $status = true;
            }
        }
        
        return [
            'message' => $message,
            'status' => $status,
        ];
    }
}

/**
 * Set Login
 *
 * @param  string  $token
 * @param  integer $user_id
 * @param  boolean $social
 * @param  boolean $api
 * @return boolean
 */
if ( ! function_exists( 'set_login' ) )
{
    function set_login( $token, $user_id, $social = false, $api = false )
    {
        $ci =& get_instance();
        $ci->load->model( 'Login_model' );
        
        if ( $ci->Login_model->add_sess( $token, $user_id, $api ) )
        {
            $ci->Login_model->set_as_online( $user_id );
            
            if ( $api === false )
            {
                $ci->Login_model->set_user_login_interface( $user_id, 1 );
                
                if ( post( 'remember_me' ) || get_session( 'remember_login' ) || $social )
                {
                    set_cookie( USER_TOKEN, $token, strtotime( '+1 year' ) );
                }
                else
                {
                    delete_cookie( USER_TOKEN );
                }
                
                set_session( USER_TOKEN, $token );
                unset_session( 'remember_login' );
                
                $ci->zuser->data->id = $user_id;
            }
            else
            {
                $ci->Login_model->set_user_login_interface( $user_id );
            }
            
            $ci->Login_model->save_last_login( $user_id );
            
            return true;
        }
        
        return false;
    }
}

/**
 * Set 2FA
 *
 * @param  object  $user
 * @param  string  $remember_login
 * @param  boolean $social
 * @return void
 */
if ( ! function_exists( 'set_2fa' ) )
{
    function set_2fa( $user, $remember_login = '', $social = false )
    {
        $ci =& get_instance();
        
        $ci->load->helper( 'string' );
        
        $template = $ci->Tool_model->email_template_by_hook_and_lang( '2f_authentication', get_language() );
        $code = random_string( 'numeric', 6 );
        
        if ( empty( $template ) ) return 'missing_template';
        
        $message = replace_placeholders( $template->template, [
            '{USER_NAME}' => $user->first_name . ' ' . $user->last_name,
            '{SITE_NAME}' => db_config( 'site_name' ),
            '{2F_CODE}' => $code
        ]);
        
        if ( ! is_email_settings_filled() )
        {
            if ( $social ) error_redirect( 'missing_email_config', 'login' );
            else r_error_c( 'missing_email_config' );
        }
        
        $ci->load->library( 'ZMailer' );
        
        if ( $ci->zmailer->send_email( $user->email_address, $template->subject, $message ) )
        {
            $ci->load->model( 'Email_token_model' );
            
            $status = $ci->Email_token_model->add_email_token( $code, $user->id, '2fa' );
            
            if ( $status )
            {
                if ( ! empty( $remember_login ) || $social )
                {
                    set_session( 'remember_login', 1 );
                }
                
                set_session( '2FA', $user->id );
                r_s_jump( '2f_authentication' );
            }
        }
        
        if ( $social ) error_redirect( 'went_wrong', 'login' );
        else r_error_c( 'went_wrong' );
    }
}

/**
 * Manage 2FA
 *
 * @param  object  $user
 * @param  boolean $social
 * @return boolean
 */
if ( ! function_exists( 'manage_2fa' ) )
{
    function manage_2fa( $user, $social = false )
    {
        if ( is_email_settings_filled() )
        {
            $ci =& get_instance();
            $ci->load->model( 'Login_model' );
        
            if ( db_config( 'u_2f_authentication' ) == 1 && $user->two_factor_authentication )
            {
                $cookie = get_cookie( PREFIX_2FA . md5( $user->id ) );
                
                if ( ! empty( $cookie ) )
                {
                    if ( ! $ci->Login_model->verify_user_remembered_2fa( $cookie, $user->id ) )
                    {
                        set_2fa( $user, post( 'remember_me' ), $social );
                    }
                }
                else
                {
                    set_2fa( $user, post( 'remember_me' ), $social );
                }
            }
        }
        
        return false;
    }
}

/**
 * EVerification Setup
 *
 * @param  integer $id
 * @return mixed
 */
if ( ! function_exists( 'everification_setup' ) )
{
    function everification_setup( $id )
    {
        $ci =& get_instance();
        
        $template = $ci->Tool_model->email_template_by_hook_and_lang( 'email_verification', get_language() );
        $token = get_short_random_string();
        
        if ( empty( $template ) ) return 'missing_template';
        
        $ci->load->model( 'User_model' );
        
        $user = $ci->User_model->get_by_id( $id );
        
        if ( empty( $user ) ) return 'invalid_req';
        
        if ( $user->is_verified != 0 ) return 'already_verified';
        
        $message = replace_placeholders( $template->template, [
            '{USER_NAME}' => $user->first_name . ' ' . $user->last_name,
            '{EMAIL_LINK}' => env_url( "everify/{$id}/{$token}" ),
            '{SITE_NAME}' => db_config( 'site_name' )
        ]);
        
        if ( ! is_email_settings_filled() ) return 'missing_email_config_a';
        
        $ci->load->library( 'ZMailer' );
        
        if ( $ci->zmailer->send_email( $user->email_address, $template->subject, $message ) )
        {
            $ci->load->model( 'Email_token_model' );
             
            $status = $ci->Email_token_model->add_email_token( $token, $id, 'email_verification' );
            
            if ( empty( $status ) )
            {
                return 'ev_token_update_failed';
            }
            
            return true;
        }
        
        return 'failed_email';
    }
}

/**
 * Set Custom Field Input
 *
 * @param  integer $id Custom Field ID
 * @param  string  $value
 * @return array
 */
if ( ! function_exists( 'set_cf_input' ) )
{
    function set_cf_input( $id, $value )
    {
        return ['custom_field_id' => $id, 'value' => $value];
    }
}

/**
 * Manage Custom Fields Input.
 *
 * @param  integer $user_id
 * @param  boolean $or
 * @return mixed
 */
if ( ! function_exists( 'manage_cf_input' ) )
{
    function manage_cf_input( $user_id, $or = false )
    {
        $ci =& get_instance();
        
        $fields = $ci->Tool_model->custom_fields( 'ASC', $or );
        $input = [];
        
        if ( ! empty( $fields ) )
        {
            foreach ( $fields as $field )
            {
                $type = $field->type;
                $id = $field->id;
                $value = '';
                
                if ( $type === 'text' || $type === 'password' || $type === 'textarea' || $type === 'email' )
                {
                    $value = do_secure( post( "cf_{$id}" ) );
                    
                    if ( ! empty( $value ) )
                    {
                        if ( $type === 'email' && ! filter_var( $value, FILTER_VALIDATE_EMAIL ) )
                        {
                             return 'invalid_input';
                        }
                        
                        $input[] = set_cf_input( $id, $value );
                    }
                }
                else if (  $type === 'checkbox' || $type === 'radio' || $type === 'select' )
                {
                    $options = explode( ',', $field->options );
                    
                    if ( $type === 'checkbox' )
                    {
                        foreach ( $options as $key => $option )
                        {
                            if ( do_secure( post( "cf_{$id}_{$key}" ) ) )
                            {
                                $option = trim( $option );
                                
                                if ( ! empty( $value ) ) $value .= ", {$option}";
                                else $value .= $option;
                            }
                        }
                    }
                    else
                    {
                        if ( post( "cf_{$id}" ) !== null && post( "cf_{$id}" ) !== '' )
                        {
                            $value = intval( post( "cf_{$id}" ) );
                            
                            foreach ( $options as $key => $option )
                            {
                                if ( $key === $value )
                                {
                                    $value = trim( $option );
                                }
                            }
                        }
                    }
                    
                    $input[] = set_cf_input( $id, $value );
                }
                
                if ( $field->is_required && empty( $value ) )
                {
                    return 'missing_input';
                }
            }
        }
        
        if ( ! empty( $user_id ) )
        {
            if ( ! empty( $input ) )
            {
                foreach ( $input as $data )
                {
                    $ci->User_model->manage_cf_data(
                    [
                        'custom_field_id' => $data['custom_field_id'],
                        'user_id' => $user_id,
                        'value' => $data['value']
                    ]);
                }
            }
        }
        
        return true;
    }
}

/**
 * Validate Custom Fields Input
 *
 * @param  boolean $or
 * @return mixed
 */
if ( ! function_exists( 'validate_cf_input' ) )
{
    function validate_cf_input( $or = true )
    {
        return manage_cf_input( 0, $or );
    }
}

/**
 * Update Profile Settings
 *
 * @param  integer $user_id
 * @param  string  $area
 * @return mixed
 */
if ( ! function_exists( 'update_profile_settings' ) )
{
    function update_profile_settings( $user_id, $area = '' )
    {
        $ci =& get_instance();
        
        $ci->load->model( 'User_model' );
        
        $user = $ci->User_model->get_by_id( $user_id );
        
        if ( empty( $user ) ) return 'invalid_req';
        
        $email_input = true;
        
        $data = [
            'first_name' => do_secure_u( post( 'first_name' ) ),
            'last_name' => do_secure_u( post( 'last_name' ) ),
            'email_address' => do_secure_l( post( 'email_address' ) ),
            'username' => do_secure_l( post( 'username' ) ),
            'about' => do_secure( post( 'about' ) ),
            'language' => do_secure_l( post( 'language' ) ),
            'country_id' => intval( post( 'country' ) ),
            'currency_id' => intval( post( 'currency' ) ),
            'gender' => do_secure_l( post( 'gender' ) ),
            'time_format' => do_secure( post( 'time_format' ) ),
            'date_format' => do_secure( post( 'date_format' ) ),
            'timezone' => do_secure( post( 'timezone' ) ),
            'state' => do_secure( post( 'state' ) ),
            'city' => do_secure( post( 'city' ) ),
            'zip_code' => do_secure( post( 'zip_code' ) ),
            'address_1' => do_secure( post( 'address_1' ) ),
            'address_2' => do_secure( post( 'address_2' ) ),
            'phone_number' => do_secure( post( 'phone_number' ) ),
            'company' => do_secure( post( 'company' ) )
        ];
        
        if ( ! empty( $data['country_id'] ) )
        {
            $result = array_search( $data['country_id'], array_column( get_countries(), 'id' ) );
            if ( $result === false ) r_error( 'invalid_req' );
        }
        
        if ( ! empty( $data['currency_id'] ) && empty( get_currency_by_id( $data['currency_id'] ) ) )
        {
            r_error( 'invalid_req' );
        }
        
        if ( db_config( 'u_2f_authentication' ) )
        {
            $data['two_factor_authentication'] = only_binary( post( 'two_factor_authentication' ) );
        }
        
        if ( db_config( 'u_allow_email_change' ) == 0 && $ci->uri->segment( 2 ) === 'user' )
        {
            unset( $data['email_address'] );
            
            $email_input = false;
        }
        else
        {
            if ( $ci->User_model->is_email_address_exists( $data['email_address'], $user_id ) )
            {
                r_error( 'email_taken' );
            }
        }
        
        if ( db_config( 'u_allow_username_change' ) == 0 && $ci->uri->segment( 2 ) === 'user' )
        {
            unset( $data['username'] );
        }
        else
        {
            if ( $ci->User_model->is_username_exists( $data['username'], $user_id ) )
            {
                r_error( 'username_taken' );
            }
        }
        
        if ( $email_input )
        {
            if ( db_config( 'u_req_ev_onchange' ) == 1 && $area === 'user' )
            {
                if ( $data['email_address'] !== $user->email_address )
                {
                    if ( $user->pending_email_address == $data['email_address'] ) return 'already_email_pending';
                    
                    $data['pending_email_address'] = $data['email_address'];
                    
                    $template = $ci->Tool_model->email_template_by_hook_and_lang( 'change_email', get_language() );
                    $token = get_short_random_string();
                    
                    if ( empty( $template ) ) r_error( 'missing_template' );
                    
                    $message = replace_placeholders( $template->template, [
                        '{EMAIL_LINK}' => env_url( "change_email/{$token}" ),
                        '{SITE_NAME}' => db_config( 'site_name' )
                    ]);
                    
                    if ( ! is_email_settings_filled() ) return 'missing_email_config';
                    
                    $ci->load->library( 'ZMailer' );
                    
                    if ( $ci->zmailer->send_email( $data['email_address'], $template->subject, $message ) )
                    {
                        $ci->load->model( 'Email_token_model' );
                        
                        if ( ! $ci->Email_token_model->add_email_token( $token, $user->id, 'change_email' ) )
                        {
                            return 'went_wrong';
                        }
                    }
                    else
                    {
                        return 'failed_email';
                    }
                    
                    unset( $data['email_address'] );
                }
            }
            else if ( $ci->uri->segment( 2 ) === 'user' )
            {
                // If the email change verification isn't required, clear the pending
                // email address or don't store the email ( for verification ) in it:
                if ( $data['email_address'] !== $user->email_address ) $data['pending_email_address'] = '';
            }
        }
        
        if ( ! empty( $_FILES['picture']['tmp_name'] ) )
        {
            $ci->load->library( 'ZFiles' );
            
            $old_file = $user->picture;
            $data['picture'] = $ci->zfiles->upload_user_avatar();
            
            if ( ! filter_var( $old_file, FILTER_VALIDATE_URL ) )
            {
                if ( $old_file !== DEFAULT_USER_IMG )
                {
                    $ci->zfiles->delete_image_file( 'users', $old_file );
                }
            }
        }
        
        if ( $area === 'admin' )
        {
            $data['is_verified'] = only_binary( post( 'email_verified' ) );
            
            if ( post( 'password' ) && ! post( 'retype_password' ) )
            {
                return 'missing_passwords';
            }
            
            if ( post( 'password' ) )
            {
                if ( post( 'password' ) == post( 'retype_password' ) )
                {
                    $status = validate_password( post( 'password' ) );
                
                    if ( $status['status'] === false ) return $status['message'];
                    
                    $data['password'] = password_hash( post( 'password' ), PASSWORD_DEFAULT );
                }
                else
                {
                    return 'passwords_match';
                }
            }
            
            $data['status'] = intval( post( 'status' ) );
            
            if ( $data['status'] == 0 && post( 'reason' ) )
            {
                $data['reason'] = do_secure( post( 'reason' ), true );
            }
            else if ( $user->status == 0 && $data['status'] == 1 )
            {
                $data['reason'] = '';
            }
            
            $data['role'] = intval( post( 'role' ) );
            
            // If password, role, or status is changed for the
            // default user, then don't allow the updation:
            if ( $user_id == 1 )
            {
                if ( $data['role'] != 1 ||
                   ( post( 'password' ) || post( 'retype_password' ) ) ||
                     $data['status'] != 1 )
                return 'u_change_not_allowed';
            }
        }
        
        $ci->User_model->update_user( $data, $user_id );
        return manage_cf_input( $user_id );
    }
}

/**
 * Delete User
 *
 * @param  integer $id
 * @return boolean
 */
if ( ! function_exists( 'delete_user' ) )
{
    function delete_user( $id )
    {
        $ci =& get_instance();
        
        $ci->load->model( 'Email_token_model' );
        $ci->load->model( 'Login_model' );
        $ci->load->model( 'User_model' );
        
        $ci->Email_token_model->delete_user_tokens( $id );
        $ci->Login_model->delete_user_rememberings( $id );
        $ci->Tool_model->delete_user_sessions( $id );
        $ci->Tool_model->delete_user_custom_fields( $id );
        $ci->Tool_model->clear_user_log( $id );
        $ci->User_model->delete_user_credit( $id );
        
        return $ci->User_model->delete_user( $id );
    }
}

/**
 * Get Lockout Unlock Time
 *
 * @param  boolean $math
 * @return mixed
 */
if ( ! function_exists( 'get_lockout_unlock_time' ) )
{
    function get_lockout_unlock_time( $math = false )
    {
        $key = db_config( 'u_lockout_unlock_time' );
        
        if ( $math )
        {
            $periods = [
                '1' => 15 * 60,
                '2' => 30 * 60,
                '3' => 60 * 60,
                '4' => 24 * 60 * 60
            ];
        }
        else
        {
            $periods = [
                '1' => '15 minutes',
                '2' => '30 minutes',
                '3' => '60 minutes',
                '4' => '24 hours'
            ];
        }
        
        if ( array_key_exists( $key, $periods ) )
        {
            return $periods[$key];
        }
    }
}

/**
 * Get Max Allowed Attempts
 *
 * @return integer
 */
if ( ! function_exists( 'get_max_allowed_attempts' ) )
{
    function get_max_allowed_attempts()
    {
        $key = db_config( 'u_temporary_lockout' );
        
        $allowed = [
            'strict' => 5,
            'medium' => 10,
            'normal' => 20
        ];
        
        if ( array_key_exists( $key, $allowed ) )
        {
            return $allowed[$key];
        }
    }
}
