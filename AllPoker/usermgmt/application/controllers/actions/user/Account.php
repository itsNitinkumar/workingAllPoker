<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Account Controller ( User, Actions )
 *
 * @author Shahzaib
 */
class Account extends MY_Controller {
    
    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        if ( ! $this->zuser->is_logged_in ) r_s_jump( 'login' );
        
        $this->load->library( 'form_validation' );
        $this->load->model( 'User_model' );
    }
    
    /**
     * Delete User Profile Picture
     *
     * @return  void
     * @version 1.6
     */
    public function delete_profile_picture()
    {
        $id = $this->zuser->get( 'id' );
        $togo = "user/account/profile_settings";
        
        if ( delete_profile_picture( $id ) )
        {
            log_user_activity( 'u_pp_deleted_by_user', $id );
            r_s_jump( $togo, 'acc_pic_deleted' );
        }
        
        r_error( 'invalid_req' );
    }
    
    /**
     * Profile Settings Page Input Handling.
     *
     * @return void
     */
    public function update_profile_settings()
    {
        if ( $this->form_validation->run( 'profile_settings' ) )
        {
            $status = update_profile_settings( $this->zuser->get( 'id' ), 'user' );
            
            if ( $status === true )
            {
                log_user_activity( 'profile_settings_updated' );
                r_s_jump( 'user/account/profile_settings', 'updated' );
            }
            
            r_error( $status );
        }
        
        d_r_error( validation_errors() );
    }
    
    /**
     * Change Password Page Input Handling.
     *
     * @return void
     */
    public function change_password()
    {
        $form_validation_key = 'change_password_whole';
        $old_password = $this->zuser->get( 'password' );
        
        if ( empty( $old_password ) )
        {
            $form_validation_key = 'change_password';
        }
        
        if ( $this->form_validation->run( $form_validation_key ) )
        {
            $this->load->model( 'Login_model' );
            
            $user_id = $this->zuser->get( 'id' );
            
            if ( db_config( 'u_temporary_lockout' ) !== 'off' )
            {
                user_locally_locked_check( $user_id, 'change_password' );
            }
            
            if ( ! empty( $old_password ) )
            {
                if ( ! password_verify( post( 'current_password' ), $old_password ) )
                {
                    $this->Login_model->log_invalid_attempt( $user_id, 'change_password' );
                    log_user_activity( 'attempted_change_pass' );
                    r_error( 'wrong_password' );
                }
                
                // Don't allow the password to be the same as before ( old password ):
                else if ( password_verify( post( 'password' ), $old_password ) )
                {
                    r_error( 'same_password' );
                }
            }
            
            $status = validate_password( post( 'password' ) );
            
            if ( $status['status'] === false ) r_error( $status['message'] );
            
            if ( $this->User_model->update_password( $user_id, post( 'password' ) ) )
            {
                if ( db_config( 'u_notify_pass_changed' ) )
                {
                    $template = $this->Tool_model->email_template_by_hook_and_lang( 'changed_password', get_language() );
                    
                    if ( empty( $template ) ) r_error( 'missing_template' );
                    
                    $first_name = $this->zuser->get( 'first_name' );
                    $last_name = $this->zuser->get( 'last_name' );
                    $email_address = $this->zuser->get( 'email_address' );
                    $subject = $template->subject;
                    
                    $message = replace_placeholders( $template->template, [
                        '{USER_NAME}' => $first_name . ' ' . $last_name,
                        '{SITE_NAME}' => db_config( 'site_name' )
                    ]);
                    
                    if ( is_email_settings_filled() )
                    {
                        $this->load->library( 'ZMailer' );
                        
                        $this->zmailer->send_email( $email_address, $subject, $message );
                    }
                }
                
                $this->Login_model->delete_invalid_attempt( $user_id, 'change_password' );
                log_user_activity( 'user_password_changed' );
                r_s_jump( 'user/account/change_password', 'updated' );
            }
            
            r_error( 'went_wrong' );
        }
        
        d_r_error( validation_errors() );
    }
    
    /**
     * Social Links Page Input Handling
     *
     * @return  void
     * @version 1.4
     */
    public function update_social_links()
    {
        $keys = ['facebook', 'twitter', 'linkedin', 'youtube'];
        
        if ( is_valid_urls_post( $keys ) )
        {
            $id = $this->zuser->get( 'id' );
            
            $data = [
                'facebook' => do_secure_url( post( 'facebook' ) ),
                'twitter' => do_secure_url( post( 'twitter' ) ),
                'linkedin' => do_secure_url( post( 'linkedin' ) ),
                'youtube' => do_secure_url( post( 'youtube' ) )
            ];
            
            if ( $this->User_model->update_user( $data, $id ) )
            {
                log_user_activity( 'social_links_updated' );
                r_s_jump( 'user/account/social_links', 'updated' );
            }
            
            r_error( 'not_updated' );
        }
        
        r_error( 'invalid_urls' );
    }
    
    /**
     * Delete Account
     *
     * @return void
     */
    public function delete()
    {
        if ( db_config( 'u_can_remove_them' ) == 0 )
        {
            r_error( 'invalid_req' );
        }
        
        $id = $this->zuser->get( 'id' );
        
        if ( $id == 1 ) r_error( 'cant_delete_du' );
        
        if ( delete_user( $id ) )
        {
            r_s_jump( 'login', 'my_acc_deleted' );
        }
        
        r_error( 'invalid_req' );
    }
}