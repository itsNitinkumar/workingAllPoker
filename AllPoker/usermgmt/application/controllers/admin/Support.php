<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Support Controller ( Admin )
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
        
        if ( ! $this->zuser->is_logged_in ) env_redirect( 'login' );
        
        if ( ! $this->zuser->has_permission( 'support' ) )
        {
            no_permission_redirect();
        }
        
        $this->sub_area = 'support';
        $this->area = 'admin';
        
        $this->load->model( 'Support_model' );
        $this->load->library( 'pagination' );
    }
    
    /**
     * Contact Messages Pages ( Not Replied, Replied ).
     *
     * @param  string $sub_page
     * @return void
     */
    public function contact_messages( $sub_page = '' )
    {
        if ( ! in_array( $sub_page, ['not_replied', 'replied'] ) )
        {
            env_redirect( 'dashboard' );
        }
        
        $this->set_admin_reference( 'support' );
        
        $config['base_url'] = env_url( "admin/support/contact_messages/{$sub_page}" );
        $config['total_rows'] = $this->Support_model->contact_messages( $sub_page, true );
        $config['per_page'] = PER_PAGE_RESULTS_PANEL;
        $offset = get_offset( $config['per_page'], 5 );
        
        $this->pagination->initialize( $config );
        $data['data']['pagination'] = $this->pagination->create_links();

        $data['data']['messages'] = $this->Support_model->contact_messages(
            $sub_page,
            false,
            $config['per_page'],
            $offset
        );

        $data['view'] = "contact_messages/{$sub_page}";
        $data['title'] = lang( 'contact_messages' );
        $data['delete_method'] = 'delete_contact_message';
        
        $this->load_panel_template( $data );
    }
    
    /**
     * Tickets Categories Page
     *
     * @return void
     */
    public function categories()
    {
        $this->set_admin_reference( 'support' );
        
        $data['data']['categories'] = $this->Support_model->categories();
        $data['delete_method'] = 'delete_category';
        $data['title'] = lang( 'categories' );
        $data['view'] = 'categories';
        
        $this->load_panel_template( $data );
    }
    
    /**
     * Tickets Page
     *
     * @return void
     */
    public function tickets()
    {
        $this->set_admin_reference( 'support' );
        
        $config['base_url'] = env_url( 'admin/support/tickets' );
        $config['total_rows'] = $this->Support_model->tickets( 0, true );
        $config['per_page'] = PER_PAGE_RESULTS_PANEL;
        $offset = get_offset( $config['per_page'] );
        
        $this->pagination->initialize( $config );
        $data['data']['pagination'] = $this->pagination->create_links();
        
        $tickets = $this->Support_model->tickets( 0, false, $config['per_page'], $offset );
        $data['data']['tickets'] = $tickets;
        $data['delete_method'] = 'delete_ticket';
        $data['title'] = lang( 'tickets' );
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
        $ticket = $this->Support_model->ticket( intval( $id ) );
        
        if ( empty( $ticket ) ) env_redirect( 'admin/support/tickets' );

        $replies = $this->Support_model->tickets_replies( $ticket->id );
        
        if ( $ticket->last_message_area === 'user' && $ticket->is_read == 0 )
        {
            $this->Support_model->update_ticket( ['is_read' => 1], $ticket->id, false );
        }
        
        $this->set_admin_reference( 'support' );
        
        $data['title'] = sub_title( lang( 'tickets' ), $ticket->id );
        $data['data']['ticket'] = $ticket;
        $data['data']['replies'] = $replies;
        $data['delete_method'] = 'delete_reply';
        $data['view'] = 'ticket';
        
        $this->load_panel_template( $data );
    }
}
