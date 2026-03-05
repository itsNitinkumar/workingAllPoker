<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Payment Controller ( Admin )
 *
 * @author Shahzaib
 */
class Payment extends MY_Controller {

    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        if ( ! $this->zuser->is_logged_in ) env_redirect( 'login' );
        
        if ( ! $this->zuser->has_permission( 'payment' ) )
        {
            no_permission_redirect();
        }
        
        $this->sub_area = 'payment';
        $this->area = 'admin';
        
        $this->load->model( 'Payment_model' );
    }
    
    /**
     * Items Page
     *
     * @return void
     */
    public function items()
    {
        $this->set_admin_reference( 'payment' );
        
        $data['data']['items'] = $this->Payment_model->items();
        $data['delete_method'] = 'delete_item';
        $data['title'] = lang( 'items' );
        $data['view'] = 'items';
        
        $this->load_panel_template( $data );
    }
    
    /**
     * Log Page
     *
     * @return void
     */
    public function log()
    {
        $this->load->library( 'pagination' );
        
        $this->set_admin_reference( 'payment' );
        
        $config['base_url'] = env_url( 'admin/payment/log' );
        
        $config['total_rows'] = $this->Payment_model->payments_log([
            'count' => true
        ]);
        
        $config['per_page'] = PER_PAGE_RESULTS_PANEL;
        $offset = get_offset( $config['per_page'] );
        
        $this->pagination->initialize( $config );
        $data['data']['pagination'] = $this->pagination->create_links();
        
        $log = $this->Payment_model->payments_log([
            'limit' => $config['per_page'],
            'offset' => $offset
        ]);
        
        $data['data']['payments_log'] = $log;
        $data['delete_method'] = 'delete_log';
        $data['title'] = lang( 'log' );
        $data['view'] = 'log';
        
        $this->load_panel_template( $data );
    }
}
