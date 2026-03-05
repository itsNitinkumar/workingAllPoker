<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Pages Controller ( Admin )
 *
 * @author Shahzaib
 */
class Pages extends MY_Controller {
    
    /**
     * Pages List Page
     *
     * @param  string $type
     * @return void
     */
    public function index( $type = 'built-in' )
    {
        if ( ! $this->zuser->is_logged_in ) env_redirect( 'login' );
        
        if ( ! $this->zuser->has_permission( 'pages' ) )
        {
            no_permission_redirect();
        }
        
        $this->load->model( 'Page_model' );
        $this->set_admin_reference( 'pages' );
        $this->area = 'admin';
        
        if ( $type === 'custom' )
        {
            $data['data']['pages'] = $this->Page_model->custom_pages();
            $data['delete_method'] = 'delete_custom_page';
            $data['title'] = lang( 'custom_pages' );
            $data['view'] = 'custom_pages';
        }
        else if ( $type === 'built-in' )
        {
            $data['data']['pages'] = $this->Page_model->pages();
            $data['view'] = 'pages';
        }
        else
        {
            env_redirect( 'dashboard' );
        }
        
        $this->load_panel_template( $data, false );
    }
    
    /**
     * Custom Pages Page
     *
     * @return  void
     * @version 1.5
     */
    public function custom()
    {
        $this->index( 'custom' );
    }
}
