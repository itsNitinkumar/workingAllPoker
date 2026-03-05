<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Tools Controller ( User )
 *
 * @author Shahzaib
 */
class Tools extends MY_Controller {
    
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
        
        $this->sub_area = 'tools';
        $this->user_id = $this->zuser->get( 'id' );
        $this->area = 'user';
        
        $this->load->library( 'pagination' );
    }
    
    /**
     * Announcements Page
     *
     * @return void
     */
    public function announcements()
    {
        $this->set_user_reference( 'announcements' );
        
        $config['base_url'] = env_url( 'user/tools/announcements' );
        $config['total_rows'] = $this->Tool_model->announcements( true );
        $config['per_page'] = PER_PAGE_RESULTS_PANEL;
        $offset = get_offset( $config['per_page'] );
        
        $this->pagination->initialize( $config );
        $data['data']['pagination'] = $this->pagination->create_links();
        
        $announcements = $this->Tool_model->announcements(
            false,
            $config['per_page'],
            $offset
        );
        
        $data['data']['announcements'] = $announcements;
        $data['view'] = 'announcements';
        
        $this->load_panel_template( $data );
    }
    
    /**
     * Announcement Page
     *
     * @param  integer $id
     * @return void
     */
    public function announcement( $id = '' )
    {
        $announcement = $this->Tool_model->announcement( intval( $id ) );
        
        if ( empty( $announcement ) ) env_redirect( 'dashboard' );
        
        $this->set_user_reference( 'announcement' );
        
        $data['data']['announcement'] = $announcement;
        $data['view'] = 'announcement';
        
        $this->load_panel_template( $data );
    }
    
    /**
     * User Sessions Page
     *
     * @return void
     */
    public function sessions()
    {
        $this->set_user_reference( 'sessions' );
        
        $config['base_url'] = env_url( 'user/sessions' );
        
        $config['total_rows'] = $this->Tool_model->user_sessions([
            'user_id' => $this->user_id,
            'count' => true
        ]);
        
        $config['per_page'] = PER_PAGE_RESULTS_PANEL;
        $offset = get_offset( $config['per_page'], 3 );
        
        $this->pagination->initialize( $config );
        $data['data']['pagination'] = $this->pagination->create_links();
        
        $sessions = $this->Tool_model->user_sessions([
            'user_id' => $this->user_id,
            'limit' => $config['per_page'],
            'offset' => $offset
        ]);
        
        $data['data']['count'] = $config['total_rows'];
        $data['data']['sessions'] = $sessions;
        $data['view'] = 'sessions';
        
        $this->load_panel_template( $data );
    }
    
    /**
     * User Activities Log Page
     *
     * @return void
     */
    public function activities_log()
    {
        $this->set_user_reference( 'activities_log' );
        
        $config['base_url'] = env_url( 'user/activities_log' );
        
        $config['total_rows'] = $this->Tool_model->activities_log([
            'user_id' => $this->user_id,
            'count' => true
        ]);
        
        $config['per_page'] = PER_PAGE_RESULTS_PANEL;
        $offset = get_offset( $config['per_page'], 3 );
        
        $this->pagination->initialize( $config );
        $data['data']['pagination'] = $this->pagination->create_links();
        
        $activities_log = $this->Tool_model->activities_log([
            'user_id' => $this->user_id,
            'limit' => $config['per_page'],
            'offset' => $offset
        ]);
        
        $data['data']['activities'] = $activities_log;
        $data['view'] = 'activities_log';
        
        $this->load_panel_template( $data );
    }
}
