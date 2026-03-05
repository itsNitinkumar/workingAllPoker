<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Subscribers Controller ( Admin )
 *
 * @author Shahzaib
 */
class Subscribers extends MY_Controller {
    
    /**
     * Subscribers Page
     *
     * @return void
     */
    public function index()
    {
        if ( ! $this->zuser->is_logged_in ) env_redirect( 'login' );
        
        if ( ! $this->zuser->has_permission( 'subscribers' ) )
        {
            no_permission_redirect();
        }
        
        $this->load->model( 'Subscriber_model' );
        $this->load->library( 'pagination' );
        $this->set_admin_reference( 'subscribers' );
        
        $this->area = 'admin';
        $config['base_url'] = env_url( 'admin/subscribers/index' );
        $config['total_rows'] = $this->Subscriber_model->subscribers( true );
        $config['per_page'] = PER_PAGE_RESULTS_PANEL;
        $offset = get_offset( $config['per_page'] );
        
        $this->pagination->initialize( $config );
        $data['data']['pagination'] = $this->pagination->create_links();
        
        $subscribers = $this->Subscriber_model->subscribers(
            false,
            $config['per_page'],
            $offset
        );
        
        $data['data']['subscribers'] = $subscribers;
        $data['view'] = 'subscribers';
        
        $this->load_panel_template( $data, false );
    }
}
