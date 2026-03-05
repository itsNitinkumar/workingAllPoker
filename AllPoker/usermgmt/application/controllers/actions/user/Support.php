<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Support Controller ( User, Actions )
 *
 * @author Shahzaib
 */
class Support extends MY_Controller {
    
    /**
     * Logged in User ID
     *
     * @var integer
     */
    private $user_id;
    
    
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
        $this->load->model( 'Support_model' );
        
        $this->user_id = $this->zuser->get( 'id' );
    }
    
    /**
     * Create Ticket Page Input Handling.
     *
     * @return void
     */
    public function create_ticket()
    {
        if ( db_config( 'sp_enable_tickets' ) == 0 )
        {
            r_error( 'temp_disabled' );
        }
        
        if ( $this->form_validation->run( 'create_ticket' ) )
        {
            $data = [
                'subject' => do_secure( post( 'subject' ) ),
                'message' => do_secure( post( 'message' ), true ),
                'priority' => do_secure_l( post( 'priority' ) ),
                'user_id' => $this->user_id,
                'category_id' => intval( post( 'category' ) ),
                'created_at' => time()
            ];
            
            if ( empty( $this->Support_model->category( $data['category_id'] ) ) )
            {
                r_error( 'invalid_category' );
            }
            
            if ( ! in_array( $data['priority'], ['low', 'medium', 'high'] ) )
            {
                r_error( 'invalid_priority' );
            }
            
            if ( ! empty( $_FILES['attachment']['tmp_name'] ) )
            {
                $this->load->library( 'ZFiles' );
                
                $file = $this->zfiles->upload_image_file( 'attachment', 'attachments', true );
                
                $data['attachment_name'] = do_secure( $file['client_name'] );
                $data['attachment'] = $file['file_name'];
            }
            
            $id = $this->Support_model->add_ticket( $data );
            
            if ( ! empty( $id ) )
            {
                log_user_activity( 'ticket_created', $id );
                r_s_jump( "user/support/ticket/{$id}", 'ticket_created' );
            }
            
            r_error( 'went_wrong' );
        }
        
        d_r_error( validation_errors() );
    }
    
    /**
     * Add Reply Input Handling.
     *
     * @return void
     */
    public function add_reply()
    {
        if ( db_config( 'sp_enable_tickets' ) == 0 )
        {
            r_error( 'temp_disabled' );
        }
        
        $ticket_id = intval( post( 'id' ) );
        
        $data = $this->Support_model->ticket( $ticket_id, $this->user_id );
        
        if ( empty( $data ) ) r_error( 'invalid_req' );
        
        if ( $data->status == 0 ) r_error( 'ticket_closed' );
        
        if ( $this->form_validation->run( 'add_reply' ) )
        {
            $data = [
                'ticket_id' => $ticket_id,
                'user_id' => $this->user_id,
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
                        'status' => 2,
                        'last_message_area' => 'user',
                        'is_read' => 0
                    ],
                    $ticket_id
                );
                
                log_user_activity( 'ticket_replied', $ticket_id );
                r_s_jump( "user/support/ticket/{$ticket_id}", 'ticket_replied' );
            }
            
            r_error( 'went_wrong' );
        }
        
        d_r_error( form_error( 'reply' ) );
    }
    
    /**
     * Close Ticket
     *
     * @return void
     */
    public function close_ticket()
    {
        $id = intval( post( 'id' ) );
        
        $data = $this->Support_model->ticket( $id, $this->user_id );
        
        if ( empty( $data ) ) r_error( 'invalid_req' );
        
        if ( $this->Support_model->close_ticket( $id ) )
        {
            log_user_activity( 'ticket_closed', $id );
            r_s_jump( "user/support/ticket/{$id}", 'ticket_closed' );
        }
        
        r_error( 'invalid_req' );
    }
}