<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Support Controller ( Admin, Actions )
 *
 * @author Shahzaib
 */
class Support extends MY_Controller {
    
    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        if ( ! $this->zuser->is_logged_in ) r_s_jump( 'login' );
        
        if ( ! $this->zuser->has_permission( 'support' ) )
        {
            r_error_no_permission();
        }
        
        $this->load->library( 'form_validation' );
        $this->load->model( 'Support_model' );
    }
    
    /**
     * Edit Ticket Reply ( Response ).
     *
     * @return  void
     * @version 2.0
     */
    public function edit_ticket_reply()
    {
        if ( ! post( 'id' ) ) r_error( 'invalid_req' );
        
        $data = $this->Support_model->ticket_reply( intval( post( 'id' ) ) );
        
        if ( ! empty( $data ) )
        {
            display_view( 'admin/responses/forms/edit_ticket_reply', $data );
        }
        
        r_error( 'invalid_req' );
    }
    
    /**
     * Update Ticket Reply Input Handling.
     *
     * @param   integer $ticket_id
     * @return  void
     * @version 2.0
     */
    public function update_ticket_reply( $ticket_id = 0 )
    {
        if ( $this->form_validation->run( 'update_ticket_reply' ) )
        {
            $id = intval( post( 'id' ) );
            
            $result = $this->Support_model->ticket_reply( $id );
            
            if ( empty( $result ) ) r_error( 'invalid_req' );
            
            $data = ['message' => do_secure( post( 'message' ), true )];
            
            if ( ! empty( $_FILES['attachment']['tmp_name'] ) )
            {
                $this->load->library( 'ZFiles' );
                
                if ( ! empty( $result->attachment ) )
                {
                    $this->zfiles->delete_image_file( 'attachments', $result->attachment );
                }
                
                $file = $this->zfiles->upload_image_file( 'attachment', 'attachments', true );
                
                $data['attachment_name'] = do_secure( $file['client_name'] );
                $data['attachment'] = $file['file_name'];
            }
            
            $ticket_id = $result->ticket_id;
            
            if ( $this->Support_model->update_reply( $data, $id ) )
            {
                log_user_activity( 'ticket_reply_updated', $ticket_id );
                
                r_s_jump( "admin/support/ticket/{$ticket_id}", 'updated' );
            }
            
            r_error( 'not_updated' );
        }
        
        d_r_error( validation_errors() );
    }
    
    /**
     * Contact Message ( Read More ) Response.
     *
     * @param  string $type
     * @return void
     */
    public function contact_message( $type = '' )
    {
        if ( ! post( 'id' ) || empty( $type ) || ! in_array( $type, ['message', 'reply'] ) )
        {
            r_error( 'invalid_req' );
        }
        
        $id = intval( post( 'id' ) );
        $data = $this->Support_model->contact_message( $id );
        
        if ( ! empty( $data ) )
        {
            if ( $type == 'message' && $data->is_read == 0 )
            {
                $this->Support_model->cm_mark_as_read( $id );
            }
            
            $data = ['detail' => $data->{$type}, 'type' => lang( $type ), 'id' => $id];
            display_view( 'admin/responses/read_more', $data );
        }
        
        r_error( 'invalid_req' );
    }
    
    /**
     * Contact Message Reply Input Handling.
     *
     * @return void
     */
    public function reply_contact_message()
    {
        if ( ! is_email_settings_filled() )
        {
            r_error( 'missing_email_config_a' );
        }
        
        if ( ! $this->form_validation->run( 'contact_message_reply' ) )
        {
            d_r_error( form_error( 'reply_message' ) );
        }
        
        $id = intval( post( 'id' ) );
        $text = do_secure( post( 'reply_message' ) );
        $data = $this->Support_model->contact_message( $id );
        
        if ( ! empty( $data ) )
        {
            $this->lang->load( 'email', 'english' );
            $this->load->library( 'ZMailer' );
            
            $subject = lang( 'e_message_reply_subject' );
            $message = lang( 'e_message_reply_message' );
            $message = sprintf( $message, $data->full_name, $data->message, $text, db_config( 'site_name' ) );
            
            if ( $this->zmailer->send_email( $data->email_address, $subject, $message ) )
            {
                if ( $this->Support_model->add_contact_message_reply( $id, $text ) )
                {
                    log_user_activity( 'contact_msg_replied', $id );
                    r_success_remove( $id, 'replied' );
                }
                
                j_error( 'went_wrong' );
            }
            
            r_error( 'failed_email' );
        }
        
        r_error( 'invalid_req' );
    }
    
    /**
     * Reply Box ( Response ).
     *
     * @return void
     */
    public function reply_box()
    {
        if ( ! post( 'id' ) ) r_error( 'invalid_req' );
        
        $data = $this->Support_model->contact_message( intval( post( 'id' ) ) );
        
        if ( ! empty( $data ) )
        {
            display_view( 'admin/responses/forms/contact_message_reply', $data );
        }
        
        r_error( 'invalid_req' );
    }
    
    /**
     * Delete Contact Message
     *
     * @return void
     */
    public function delete_contact_message()
    {
        $id = intval( post( 'id' ) );
        
        if ( $this->Support_model->delete_contact_message( $id ) )
        {
            log_user_activity( 'contact_message_deleted', $id );
            r_success_remove( $id );
        }
        
        r_error( 'invalid_req' );
    }
    
    /**
     * Add Category Input Handling.
     *
     * @return void
     */
    public function add_category()
    {
        if ( $this->form_validation->run( 'tickets_category' ) )
        {
            $data = [
                'name' => do_secure( post( 'category' ) ),
                'created_at' => time()
            ];
            
            $id = $this->Support_model->add_category( $data );
            
            if ( ! empty( $id ) )
            {
                $data['id'] = $id;
                
                $html = read_view( 'admin/responses/add_tickets_category', $data );
                
                log_user_activity( 'tickets_category_added', $id );
                r_success_add( $html );
            }
            
            r_error( 'went_wrong' );
        }
        
        d_r_error( form_error( 'category' ) );
    }
    
    /**
     * Edit Category ( Response ).
     *
     * @return void
     */
    public function edit_category()
    {
        if ( ! post( 'id' ) ) r_error( 'invalid_req' );
        
        $data = $this->Support_model->category( intval( post( 'id' ) ) );
        
        if ( ! empty( $data ) )
        {
            display_view( 'admin/responses/forms/edit_tickets_category', $data );
        }
        
        r_error( 'invalid_req' );
    }
    
    /**
     * Update Category Input Handling.
     *
     * @return void
     */
    public function update_category()
    {
        if ( $this->form_validation->run( 'tickets_category' ) )
        {
            $id = intval( post( 'id' ) );
            
            $data = [
                'name' => do_secure( post( 'category' ) )
            ];
            
            if ( $this->Support_model->update_category( $data, $id ) )
            {
                $data = $this->Support_model->category( $id );
                $html = read_view( 'admin/responses/update_tickets_category', $data );
                
                log_user_activity( 'tickets_category_updated', $id );
                r_success_replace( $id, $html );
            }
            
            r_error( 'not_updated' );
        }
        
        d_r_error( form_error( 'category' ) );
    }
    
    /**
     * Delete Category.
     *
     * @return void
     */
    public function delete_category()
    {
        $id = intval( post( 'id' ) );
        
        if ( $this->Support_model->delete_category( $id ) )
        {
            log_user_activity( 'tickets_category_deleted', $id );
            r_success_remove( $id );
        }
        
        r_error( 'invalid_req' );
    }
    
    /**
     * Send Reply Notification
     *
     * Use to send the ticket replied notification if the
     * reporter user is offline.
     *
     * @param  integer $user_id
     * @return boolean
     */
    private function send_reply_notification( $user_id )
    {
        $this->load->model( 'User_model' );
        
        $reporter = $this->User_model->get_by_id( $user_id );
        
        if ( $reporter->is_online == 0 )
        {
            if ( ! is_email_settings_filled() ) return false;
            
            $email_address = $reporter->email_address;
            $language = get_user_closer_language( $reporter->language );
            $hook = 'ticket_reply_notification';
            $template = $this->Tool_model->email_template_by_hook_and_lang( $hook, $language );

            if ( empty( $template ) ) return false;
            
            $subject = $template->subject;
            $message = replace_placeholders( $template->template, [
                '{USER_NAME}' => $reporter->first_name . ' ' . $reporter->last_name,
                '{SITE_NAME}' => db_config( 'site_name' )
            ]);
            
            $this->load->library( 'ZMailer' );

            if ( $this->zmailer->send_email( $email_address, $subject, $message ) )
            {
                return true;
            }

            return false;
        }
        else
        {
            return true;
        }
    }
    
    /**
     * Add Reply Input Handling.
     *
     * @return void
     */
    public function add_reply()
    {
        $ticket_id = intval( post( 'id' ) );
        
        $ticket = $this->Support_model->ticket( $ticket_id );
        
        if ( empty( $ticket ) ) r_error( 'invalid_req' );
        
        if ( $ticket->status == 0 ) r_error( 'ticket_closed' );
        
        if ( $this->form_validation->run( 'add_reply' ) )
        {
            $data = [
                'ticket_id' => $ticket_id,
                'user_id' => $this->zuser->get( 'id' ),
                'message' => do_secure( post( 'reply' ), true ),
                'replied_at' => time()
            ];
            
            if ( ! empty( $_FILES['attachment']['tmp_name'] ) )
            {
                $this->load->library( 'ZFiles' );
                
                $file = $this->zfiles->upload_image_file( 'attachment', 'attachments', true );
                
                $data['attachment_name'] = do_secure( $file['client_name'] );
                $data['attachment'] = $file['file_name'];
            }
            
            $id = $this->Support_model->add_reply( $data );
            
            if ( ! empty( $id ) )
            {
                $this->Support_model->update_ticket(
                    [
                        'status' => 3,
                        'last_message_area' => 'admin',
                        'is_read' => 0
                    ],
                    $ticket_id
                );
                
                if ( db_config( 'sp_notify_replies' ) )
                if ( ! $this->send_reply_notification( $ticket->user_id ) )
                {
                    r_error( 'ticket_fe' );
                }
                
                log_user_activity( 'ticket_replied', $ticket_id );
                r_s_jump( "admin/support/ticket/{$ticket_id}", 'ticket_replied' );
            }
            
            r_error( 'went_wrong' );
        }
        
        d_r_error( form_error( 'reply' ) );
    }
    
    /**
     * Re-open Ticket
     *
     * @return void
     */
    public function reopen_ticket()
    {
        $id = intval( post( 'id' ) );
        
        $data = $this->Support_model->ticket( $id );
        
        if ( empty( $data ) ) r_error( 'invalid_req' );
        
        if ( $this->Support_model->reopen_ticket( $id ) )
        {
            log_user_activity( 'ticket_reopened', $id );
            r_s_jump( "admin/support/ticket/{$id}", 'ticket_reopened' );
        }
        
        r_error( 'invalid_req' );
    }
    
    /**
     * Close Ticket
     *
     * @return void
     */
    public function close_ticket()
    {
        $id = intval( post( 'id' ) );
        
        $data = $this->Support_model->ticket( $id );
        
        if ( empty( $data ) ) r_error( 'invalid_req' );
        
        if ( $this->Support_model->close_ticket( $id ) )
        {
            log_user_activity( 'ticket_closed', $id );
            r_s_jump( "admin/support/ticket/{$id}", 'ticket_closed' );
        }
        
        r_error( 'invalid_req' );
    }
    
    /**
     * Delete Ticket Reply
     *
     * @return void
     */
    public function delete_reply()
    {
        $id = intval( post( 'id' ) );
        
        $reply = $this->Support_model->ticket_reply( $id );
        
        if ( empty( $reply ) ) r_error( 'invalid_req' );
        
        $attachment = $reply->attachment;
        
        if ( $this->Support_model->delete_ticket_reply( $id ) )
        {
            if ( ! empty( $attachment ) )
            {
                $this->load->library( 'ZFiles' );
                
                $this->zfiles->delete_image_file( 'attachments', $attachment );
            }
            
            log_user_activity( 'ticket_reply_deleted', $id );
            r_s_jump( "admin/support/ticket/{$reply->ticket_id}", 'tr_deleted' );
        }
        
        r_error( 'went_wrong' );
    }
    
     /**
     * Delete Ticket
     *
     * @return void
     */
    public function delete_ticket()
    {
        $id = intval( post( 'id' ) );
        
        $ticket = $this->Support_model->ticket( $id );
        
        if ( empty( $ticket ) ) r_error( 'invalid_req' );
        
        $attachment = $ticket->attachment;
        
        if ( $this->Support_model->delete_ticket( $id ) )
        {
            $this->load->library( 'ZFiles' );
            
            $replies = $this->Support_model->tickets_replies( $id );
            
            if ( ! empty( $replies ) )
            {
                // Delete the ticket replies attachment(s) ( if attached ):
                foreach ( $replies as $reply )
                {
                    if ( ! empty( $reply->attachment ) )
                    {
                        $this->zfiles->delete_image_file(
                            'attachments',
                            $reply->attachment
                        );
                    }
                }
            }
            
            // Delete the main attachment of the ticket ( if attached ):
            if ( ! empty( $attachment ) )
            {
                $this->zfiles->delete_image_file(
                    'attachments',
                    $attachment
                );
            }
            
            $this->Support_model->delete_ticket_replies( $id );
            log_user_activity( 'ticket_deleted', $id );
            r_success_remove( $id );
        }
        
        r_error( 'went_wrong' );
    }
}
