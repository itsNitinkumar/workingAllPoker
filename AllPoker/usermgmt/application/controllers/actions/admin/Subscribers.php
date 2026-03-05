<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Subscribers Controller ( Admin, Actions )
 *
 * @author Shahzaib
 */
class Subscribers extends MY_Controller {
    
    /**
     * Class Constructor
     *
     * @return  void
     * @version 2.1
     */
    public function __construct()
    {
        parent::__construct();
        
        if ( ! $this->zuser->is_logged_in ) r_s_jump( 'login' );
        
        if ( ! $this->zuser->has_permission( 'subscribers' ) )
        {
            r_error_no_permission();
        }
        
        $this->load->library( 'form_validation' );
        $this->load->model( 'Subscriber_model' );
    }
    
    /**
     * Send Email to Subscribers
     *
     * @return  void
     * @version 2.1
     */
    public function send_email()
    {
        if ( ! is_email_settings_filled() ) r_error( 'missing_email_config_a' );
        
        if ( $this->form_validation->run( 'newsletter_email' ) )
        {
            $subject = do_secure( post( 'subject' ) );
            $message = do_secure( post( 'message' ) );
            $language = do_secure_l( post( 'language' ) );
            $template = $this->Tool_model->email_template_by_hook_and_lang( 'newsletter_email', $language );
                
            if ( empty( $template ) ) r_error( 'missing_template' );
            
            $replaced_subject = replace_placeholders( $template->subject, ['{SUBJECT}' => $subject] );
            $subscribers = $this->Subscriber_model->confirmed_subscribers();
            
            if ( ! empty( $subscribers ) )
            {
                $this->load->library( 'ZMailer' );
                
                foreach ( $subscribers as $subscriber )
                {
                    $url_unsubscribe = env_url( "unsubscribe/{$subscriber->authentication_token}" );
                    
                    $to_send = replace_placeholders( $template->template, [
                        '{MESSAGE}' => $message,
                        '{UNSUB_LINK}' => $url_unsubscribe
                    ]);
                    
                    $this->zmailer->send_email( $subscriber->email_address, $replaced_subject, $to_send );
                }
                
                log_user_activity( 'nl_sent_email' );
                r_s_jump( 'admin/subscribers', 'nl_send_cmd_done' );
            }
            
            r_error( 'no_confirmed_sub' );
        }
        
        d_r_error( validation_errors() );
    }
    
    /**
     * Delete Subscriber
     *
     * @return void
     */
    public function delete()
    {
        $id = intval( post( 'id' ) );
        
        if ( $this->Subscriber_model->delete_subscriber( $id ) )
        {
            log_user_activity( 'subscriber_deleted', $id );
            r_success_remove( $id );
        }
        
        r_error( 'invalid_req' );
    }
}
