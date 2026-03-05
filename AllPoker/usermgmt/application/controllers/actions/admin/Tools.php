<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Tools Controller ( Admin, Actions )
 *
 * @author Shahzaib
 */
class Tools extends MY_Controller {
    
    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        if ( ! $this->zuser->is_logged_in ) r_s_jump( 'login' );
        
        if ( ! $this->zuser->has_permission( 'tools' ) )
        {
            r_error_no_permission();
        }
        
        $this->load->library( 'form_validation' );
        $this->load->helper( 'z_backup' );
    }
    
    /**
     * Send Email Template Input Handling.
     *
     * @return void
     */
    public function send_email_template()
    {
        if ( $this->form_validation->run( 'just_email_address' ) )
        {
            $id = intval( post( 'id' ) );
            $template = $this->Tool_model->email_template( $id );
            
            if ( empty( $template ) ) r_error( 'invalid_req' );
            else if ( ! is_email_settings_filled() ) r_error( 'missing_email_config_a' );
            
            $this->load->library( 'ZMailer' );
            
            $email_address = do_secure_l( post( 'email_address' ) );
            $subject = $template->subject;
            $message = $template->template;
            
            if ( $this->zmailer->send_email( $email_address, $subject, $message ) )
            {
                log_user_activity( 'email_template_tested', $id );
                r_success( 'email_sent' );
            }
            
            r_error( 'failed_email' );
        }
        
        d_r_error( form_error( 'email_address' ) );
    }
    
    /**
     * Test Email Template ( Response ).
     *
     * @return void
     */
    public function test_email_template()
    {
        if ( ! post( 'id' ) ) r_error( 'invalid_req' );
        
        $data = $this->Tool_model->email_template( intval( post( 'id' ) ) );
        
        if ( ! empty( $data ) )
        {
            display_view( 'admin/responses/forms/test_email_template', ['id' => $data->id] );
        }
        
        r_error( 'invalid_req' );
    }
    
    /**
     * Summernote Image Upload
     *
     * @return  void
     * @version 1.9
     */
    public function sn_image_upload()
    {
        if ( ! empty( $_FILES['file']['name'] ) )
        {
            $this->load->library( 'ZFiles' );
            
            $file = $this->zfiles->upload_image_file( 'file', 'attachments' );
            
            echo attachments_uploads( $file );
        }
    }
    
    /**
     * Add Announcement Input Handling.
     *
     * @return void
     */
    public function add_announcement()
    {
        if ( $this->form_validation->run( 'announcement' ) )
        {
            $data = [
                'subject' => do_secure( post( 'subject' ) ),
                'announcement' => do_secure( post( 'announcement' ), true ),
                'created_at' => time()
            ];
            
            $id = $this->Tool_model->add_announcement( $data );
            
            if ( ! empty( $id ) )
            {
                $data['id'] = $id;
                
                $html = read_view( 'admin/responses/add_announcement', $data );
                
                log_user_activity( 'announcement_added', $id );
                r_success_add( $html );
            }
            
            r_error( 'went_wrong' );
        }
        
        d_r_error( validation_errors() );
    }
    
    /**
     * Announcement ( Read More ) Response.
     *
     * @return void
     */
    public function announcement()
    {
        $id = intval( post( 'id' ) );
        $data = $this->Tool_model->announcement( $id );
        
        if ( ! empty( $data ) )
        {
            $data = [
                'detail' => $data->announcement,
                'type' => lang( 'announcement' ),
                'id' => $id
            ];
            
            display_view( 'admin/responses/read_more', $data );
        }
        
        r_error( 'invalid_req' );
    }
    
    /**
     * Edit Announcement ( Response ).
     *
     * @return void
     */
    public function edit_announcement()
    {
        if ( ! post( 'id' ) ) r_error( 'invalid_req' );
        
        $data = $this->Tool_model->announcement( intval( post( 'id' ) ) );
        
        if ( ! empty( $data ) )
        {
            display_view( 'admin/responses/forms/edit_announcement', $data );
        }
        
        r_error( 'invalid_req' );
    }
    
    /**
     * Update Announcement Input Handling.
     *
     * @return void
     */
    public function update_announcement()
    {
        if ( $this->form_validation->run( 'announcement' ) )
        {
            $id = intval( post( 'id' ) );
            
            $data = [
                'subject' => do_secure( post( 'subject' ) ),
                'announcement' => do_secure( post( 'announcement' ), true )
            ];
            
            if ( $this->Tool_model->update_announcement( $data, $id ) )
            {
                $data = $this->Tool_model->announcement( $id );
                $html = read_view( 'admin/responses/update_announcement', $data );
                
                log_user_activity( 'announcement_updated', $id );
                r_success_replace( $id, $html );
            }
            
            r_error( 'not_updated' );
        }
        
        d_r_error( validation_errors() );
    }
    
    /**
     * Delete Announcement
     *
     * @return void
     */
    public function delete_announcement()
    {
        $id = intval( post( 'id' ) );
        
        if ( $this->Tool_model->delete_announcement( $id ) )
        {
            log_user_activity( 'announcement_deleted', $id );
            r_success_remove( $id );
        }
        
        r_error( 'invalid_req' );
    }
    
    /**
     * Option Required
     *
     * @param  string $type
     * @param  string $options Comma Separated
     * @return void
     */
    private function option_required( $type, $options )
    {
        $types = ['checkbox', 'radio', 'select'];
        
        if ( in_array( $type, $types ) && empty( $options ) ) r_error( 'options_req' );
    }
    
    /**
     * Add Custom Field Input Handling.
     *
     * @return void
     */
    public function add_custom_field()
    {
        if ( $this->form_validation->run( 'custom_field' ) )
        {
            $data = [
                'name' => do_secure( post( 'name' ) ),
                'on_registeration' => only_binary( post( 'on_registeration' ) ),
                'is_required' => only_binary( post( 'is_required' ) ),
                'guide_text' => do_secure( post( 'guide_text' ) ),
                'type' => do_secure_l( post( 'type' ) ),
                'options' => do_secure( post( 'options' ) ),
                'created_at' => time()
            ];
            
            $this->option_required( $data['type'], $data['options'] );
            
            $id = $this->Tool_model->add_custom_field( $data );
                
            if ( ! empty( $id ) )
            {
                $data['id'] = $id;
                
                $html = read_view( 'admin/responses/add_custom_field', $data );
                
                log_user_activity( 'custom_field_added', $id );
                r_success_add( $html );
            }
            
            r_error( 'went_wrong' );
        }
        
        d_r_error( validation_errors() );
    }
    
    /**
     * Edit Custom Field ( Response ).
     *
     * @return void
     */
    public function edit_custom_field()
    {
        if ( ! post( 'id' ) ) r_error( 'invalid_req' );
        
        $data = $this->Tool_model->custom_field( intval( post( 'id' ) ) );
        
        if ( ! empty( $data ) )
        {
            display_view( 'admin/responses/forms/edit_custom_field', $data );
        }
        
        r_error( 'invalid_req' );
    }
    
    /**
     * Update Custom Field Input Handling.
     *
     * @return void
     */
    public function update_custom_field()
    {
        if ( $this->form_validation->run( 'custom_field' ) )
        {
            $id = intval( post( 'id' ) );
            
            $data = [
                'name' => do_secure( post( 'name' ) ),
                'on_registeration' => only_binary( post( 'on_registeration' ) ),
                'is_required' => only_binary( post( 'is_required' ) ),
                'guide_text' => do_secure( post( 'guide_text' ) ),
                'type' => do_secure_l( post( 'type' ) ),
                'options' => do_secure( post( 'options' ) )
            ];
            
            $this->option_required( $data['type'], $data['options'] );
            
            if ( $this->Tool_model->update_custom_field( $data, $id ) )
            {
                $data = $this->Tool_model->custom_field( $id );
                $html = read_view( 'admin/responses/update_custom_field', $data );
                
                log_user_activity( 'custom_field_updated', $id );
                r_success_replace( $id, $html );
            }
            
            r_error( 'not_updated' );
        }
        
        d_r_error( validation_errors() );
    }
    
    /**
     * Delete Custom Field
     *
     * @return void
     */
    public function delete_custom_field()
    {
        $id = intval( post( 'id' ) );
        
        if ( $this->Tool_model->delete_custom_field( $id ) )
        {
            $this->Tool_model->delete_ucf_by_id( $id );
            log_user_activity( 'custom_field_deleted', $id );
            r_success_remove( $id );
        }
        
        r_error( 'invalid_req' );
    }
    
    /**
     * Add Email Template Input Handling.
     *
     * @return void
     */
    public function add_email_template()
    {
        if ( $this->form_validation->run( 'email_template' ) )
        {
            $lang_key = do_secure( post( 'language' ) );
            $hook = do_secure( post( 'hook' ) );
            
            if ( ! $this->Tool_model->email_template_by_hook_and_lang( $hook, $lang_key ) )
            {
                $data = [
                    'title' => do_secure( post( 'title' ) ),
                    'subject' => do_secure( post( 'subject' ) ),
                    'language' => $lang_key,
                    'hook' => $hook,
                    'template' => do_secure( post( 'template' ), true ),
                    'created_at' => time()
                ];
                
                $id = $this->Tool_model->add_email_template( $data );
                
                if ( ! empty( $id ) )
                {
                    log_user_activity( 'email_template_added', $id );
                    r_s_jump( 'admin/tools/email_templates', 'added' );
                }
                
                r_error( 'went_wrong' );
            }
            
            r_error( 'et_exists' );
        }
        
        d_r_error( validation_errors() );
    }
    
    /**
     * Update Email Template Input Handling.
     *
     * @return void
     */
    public function update_email_template()
    {
        if ( $this->form_validation->run( 'email_template' ) )
        {
            $id = intval( post( 'id' ) );
            
            $data = [
                'title' => do_secure( post( 'title' ) ),
                'subject' => do_secure( post( 'subject' ) ),
                'template' => do_secure( post( 'template' ), true )
            ];
            
            if ( $this->Tool_model->update_email_template( $data, $id ) )
            {
                log_user_activity( 'email_template_updated', $id );
                r_s_jump( 'admin/tools/email_templates', 'updated' );
            }
            
            r_error( 'not_updated' );
        }
        
        d_r_error( validation_errors() );
    }
    
    /**
     * Delete Email Template
     *
     * @return void
     */
    public function delete_email_template()
    {
        $id = intval( post( 'id' ) );
        
        $data = $this->Tool_model->email_template( $id );
        
        if ( ! empty( $data ) )
        {
            if ( $data->is_built_in == 1 ) r_error( 'invalid_req' );
            
            if ( $this->Tool_model->delete_email_template( $id ) )
            {
                log_user_activity( 'email_template_deleted', $id );
                r_s_jump( 'admin/tools/email_templates', 'deleted' );
            }
        }
        
        r_error( 'invalid_req' );
    }
    
    /**
     * Block IP Address Input Handling.
     *
     * @return void
     */
    public function block_ip_address()
    {
        if ( $this->form_validation->run( 'block_ip_address' ) )
        {
            if ( post( 'ip_address' ) === $this->input->ip_address() )
            {
                r_error( 'cant_block' );
            }
            
            $data = [
                'ip_address' => do_secure( post( 'ip_address' ) ),
                'reason' => do_secure( post( 'reason' ) ),
                'blocked_at' => time()
            ];
            
            $id = $this->Tool_model->block_ip_address( $data );
            
            if ( ! empty( $id ) )
            {
                $data['id'] = $id;
                
                $html = read_view( 'admin/responses/add_ip_address', $data );
                
                log_user_activity( 'ip_address_blocked', $data['ip_address'] );
                r_success_add( $html );
            }
            
            r_error( 'went_wrong' );
        }
        
        d_r_error( form_error( 'ip_address' ) );
    }
    
    /**
     * IP Address Blocking Reason ( Read More ) Response.
     *
     * @return void
     */
    public function ip_blocking_reason()
    {
        $id = intval( post( 'id' ) );
        $data = $this->Tool_model->blocked_ip_address( $id, 'id' );
        
        if ( ! empty( $data ) )
        {
            $data = [
                'detail' => $data->reason,
                'type' => lang( 'reason' ),
                'id' => $id
            ];
            
            display_view( 'admin/responses/read_more', $data );
        }
        
        r_error( 'invalid_req' );
    }
    
    /**
     * IP Address Geolocation Response.
     *
     * @return  void
     * @version 1.5
     */
    public function ip_geolocation()
    {
        $token = db_config( 'ipinfo_token' );
        
        if ( empty( $token ) ) r_error( 'invalid_req' );
        
        $ip = do_secure( post( 'id' ) );
        $url = "http://ipinfo.io/{$ip}?token={$token}";
        $ch = curl_init();
        
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_URL, $url );
        
        $result = curl_exec( $ch );
        
        curl_close( $ch );
        
        $response = json_decode( $result, true );
        
        if ( ! empty( $response ) )
        {
            if ( empty( $response['error'] ) )
            {
                display_view( 'admin/responses/ip_geolocation', $response );
            }
            
            $error = $response['error']['message'];
            
            d_r_error( $error );
        }
        
        r_error( 'went_wrong' );
    }
    
    /**
     * Delete IP Address.
     *
     * @return void
     */
    public function delete_ip_address()
    {
        $id = intval( post( 'id' ) );
        
        $row = $this->Tool_model->blocked_ip_address( $id, 'id' );
        $ip = $row->ip_address;
        
        if ( ! empty( $row ) )
        {
            if ( $this->Tool_model->delete_ip_address( $id ) )
            {
                log_user_activity( 'ip_address_deleted', $ip );
                r_success_remove( $id );
            }
        }
        
        r_error( 'invalid_req' );
    }
    
    /**
     * Clear Log ( Users )
     *
     * @return void
     */
    public function clear_log()
    {
        if ( ! $this->zuser->has_permission( 'users' ) )
        {
            r_error_no_permission();
        }
        
        $period = intval( post( 'period' ) );
        
        if ( $this->Tool_model->clear_log( $period ) )
        {
            log_user_activity( 'log_cleared' );
            r_s_jump( 'admin/tools/activities_log', 'log_cleared' );
        }
        
        r_error( 'av_not_found' );
    }
    
    /**
     * Backup Page Input Handling.
     *
     * @return void
     */
    public function take_backup()
    {
        if ( ! $this->zuser->has_permission( 'backup' ) )
        {
            no_permission_redirect();
        }
        
        $option = intval( post( 'option' ) );
        $action = intval( post( 'action' ) );
        $req = 'admin/tools/backup';
        
        if ( $option === 1 || $option === 2 || $option === 4 ) $to_call = 'backup_files';
        else if ( $option === 3 || $option === 5 ) $to_call = 'backup_database';
        
        if ( ! empty( $to_call ) )
        {
            log_user_activity( 'taken_backup' );
            
            if ( $option === 1 )
            {
                $backup_file = $to_call( $action, 'application/language', 1 );
            }
            else if ( $option === 4 )
            {
                $backup_file = $to_call( $action, 'uploads', 4 );
            }
            else if ( $option === 5 )
            {
                $backup_file = $to_call( $action, false );
            }
            else
            {
                $backup_file = $to_call( $action );
            }
        }
        
        if ( $action === 2 )
        {
            success_redirect( 'backup_saved', $req );
        }
    }
    
    /**
     * Delete Backup Log
     *
     * @return void
     */
    public function delete_a_backup_log()
    {
        if ( ! $this->zuser->has_permission( 'backup' ) )
        {
            r_error_no_permission();
        }
        
        $id = intval( post( 'id' ) );
        
        if ( $this->Tool_model->delete_a_backup_log( $id ) )
        {
            log_user_activity( 'backup_log_deleted', $id );
            r_success_remove( $id );
        }
        
        r_error( 'invalid_req' );
    }
    
    /**
     * Delete User Session
     *
     * @return void
     */
    public function delete_user_session()
    {
        if ( ! $this->zuser->has_permission( 'users' ) )
        {
            r_error_no_permission();
        }
        
        $id = intval( post( 'id' ) );;
        $session = $this->Tool_model->user_session( $id );
        $current = false;
        
        if ( empty( $session ) ) r_error( 'invalid_req' );
        
        if ( $this->Tool_model->delete_user_session( $id ) )
        {
            log_user_activity( 'session_deleted_admin', $session->user_id );
            
            if ( $session->token == get_session( USER_TOKEN ) )
            {
                $current = true;
            }
            
            if ( $current )
            {
                r_s_jump( 'login', 'u_sess_deleted' );
            }
            
            r_success_remove( $id, 'u_sess_deleted' );
        }
        
        r_error( 'went_wrong' );
    }
    
    /**
     * Logout All
     *
     * @return  void
     * @version 1.3
     */
    public function logout_all()
    {
        if ( ! $this->zuser->has_permission( 'users' ) )
        {
            r_error_no_permission();
        }
        
        $username = do_secure_l( post( 'username' ) );
        $togo = 'admin/users/sessions/';
        
        if ( empty( $username ) ) r_error( 'invalid_req' );
        
        $this->load->model( 'User_model' );
        
        $user = $this->User_model->get_by_username( $username );
        
        if ( ! empty( $user ) )
        {
            if ( $this->Tool_model->delete_user_sessions( $user->id ) )
            {
                log_user_activity( 'user_logged_out_all', $user->id );
                
                if ( $this->zuser->get( 'id' ) != $user->id )
                {
                    r_s_jump( $togo . $username, 'logout_all' );
                }
                
                r_s_jump( 'login', 'logout_all_self' );
            }
        }
        
        r_error( 'went_wrong' );
    }
}
