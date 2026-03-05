<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Support Controller
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
        
        if ( ! $this->zuser->is_logged_in ) env_redirect( 'login' );
        
        $this->sub_area = 'support';
        $this->user_id = $this->zuser->get( 'id' );
        $this->area = 'user';
        
        $this->load->model( 'Support_model' );
    }
    
    /**
     * Tickets Page
     *
     * @return void
     */
    public function tickets()
    {
        if ( db_config( 'sp_enable_tickets' ) == 0 )
        {
            error_redirect( 'temp_disabled', 'dashboard' );
        }
        
        $this->load->library( 'pagination' );
        $this->set_user_reference( 'tickets' );
        
        $config['base_url'] = env_url( 'user/support/tickets' );
        $config['total_rows'] = $this->Support_model->tickets( $this->user_id, true );
        $config['per_page'] = PER_PAGE_RESULTS_PANEL;
        $offset = get_offset( $config['per_page'] );
        
        $this->pagination->initialize( $config );
        $data['data']['pagination'] = $this->pagination->create_links();
        
        $tickets = $this->Support_model->tickets(
            $this->user_id,
            false,
            $config['per_page'],
            $offset
        );
        
        $data['data']['tickets'] = $tickets;
        $data['view'] = 'tickets';
        
        $this->load_panel_template( $data );
    }
    
    /**
     * Ticket Page
     *
     * @param  integer $id
     * @return void
     */
    public function ticket( $id = null )
    {
        if ( db_config( 'sp_enable_tickets' ) == 0 ) error_redirect( 'temp_disabled', 'dashboard' );
        
        $ticket = $this->Support_model->ticket( intval( $id ), $this->user_id );
        
        if ( empty( $ticket ) ) env_redirect( 'user/support/tickets' );

        $replies = $this->Support_model->tickets_replies( $ticket->id );
        
        if ( $ticket->last_message_area === 'admin' && $ticket->is_read == 0 )
        {
            $this->Support_model->update_ticket( ['is_read' => 1], $ticket->id, false );
        }
        
        $this->set_user_reference( 'tickets' );
        
        $data['data']['ticket'] = $ticket;
        $data['data']['replies'] = $replies;
        $data['title'] = $ticket->id;
        $data['view'] = 'ticket';
        
        $this->load_panel_template( $data );
    }
    
    /**
     * Create Ticket Page
     *
     * @return void
     */
    public function create_ticket()
    {
        if ( db_config( 'sp_enable_tickets' ) == 0 )
        {
            error_redirect( 'temp_disabled', 'user/support/tickets' );
        }
        
        $this->set_user_reference( 'tickets' );
        
        $data['data']['categories'] = $this->Support_model->categories();
        $data['title'] = lang( 'create_ticket' );
        $data['view'] = 'create_ticket';
        
        $this->load_panel_template( $data );
    }
}
