<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Tools Controller ( Admin )
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
        
        if ( ! $this->zuser->is_logged_in ) env_redirect( 'login' );
        
        if ( ! $this->zuser->has_permission( 'tools' ) )
        {
            no_permission_redirect();
        }
        
        $this->sub_area = 'tools';
        $this->area = 'admin';
        
        $this->load->library( 'pagination' );
    }
    
    /**
     * Announcements Page
     *
     * @return void
     */
    public function announcements()
    {
        $this->set_admin_reference( 'tools' );
        
        $config['base_url'] = env_url( 'admin/tools/announcements' );
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
        $data['delete_method'] = 'delete_announcement';
        $data['title'] = lang( 'announcements' );
        $data['view'] = 'announcements';
        
        $this->load_panel_template( $data );
    }
    
    /**
     * Custom Fields Page
     *
     * @return void
     */
    public function custom_fields()
    {
        $this->set_admin_reference( 'tools' );
        
        $data['data']['custom_fields'] = $this->Tool_model->custom_fields();
        $data['delete_method'] = 'delete_custom_field';
        $data['title'] = lang( 'custom_fields' );
        $data['view'] = 'custom_fields';
        
        $this->load_panel_template( $data );
    }
    
    /**
     * Email Templates Page
     *
     * @return void
     */
    public function email_templates()
    {
        $this->set_admin_reference( 'tools' );
        
        $data['data']['templates'] = $this->Tool_model->email_templates();
        $data['delete_method'] = 'delete_email_template';
        $data['title'] = lang( 'email_templates' );
        $data['view'] = 'email_templates';
        
        $this->load_panel_template( $data );
    }
    
    /**
     * IP Manager Page
     *
     * @return void
     */
    public function ip_manager()
    {
        $this->set_admin_reference( 'tools' );
        
        $config['base_url'] = env_url( 'admin/tools/ip_manager' );
        $config['total_rows'] = $this->Tool_model->blocked_ip_addresses( true );
        $config['per_page'] = PER_PAGE_RESULTS_PANEL;
        $offset = get_offset( $config['per_page'] );
        
        $this->pagination->initialize( $config );
        $data['data']['pagination'] = $this->pagination->create_links();
        
        $ips = $this->Tool_model->blocked_ip_addresses(
            false,
            $config['per_page'],
            $offset
        );
        
        $data['data']['ips'] = $ips;
        $data['delete_method'] = 'delete_ip_address';
        $data['title'] = lang( 'ip_manager' );
        $data['view'] = 'ip_manager';
        
        $this->load_panel_template( $data );
    }
    
    /**
     * Sessions Page
     *
     * @return  void
     * @version 1.8
     */
    public function sessions()
    {
        if ( ! $this->zuser->has_permission( 'users' ) )
        {
            no_permission_redirect();
        }
        
        $this->set_admin_reference( 'tools' );
        
        $config['base_url'] = env_url( 'admin/tools/sessions' );
        $config['total_rows'] = $this->Tool_model->user_sessions( ['count' => true] );
        $config['per_page'] = PER_PAGE_RESULTS_PANEL;
        $offset = get_offset( $config['per_page'] );
        
        $this->pagination->initialize( $config );
        
        $data['data']['pagination'] = $this->pagination->create_links();
        
        $sessions = $this->Tool_model->user_sessions([
            'limit' => $config['per_page'],
            'offset' => $offset
        ]);
        
        $data['data']['count'] = $config['total_rows'];
        $data['data']['sessions'] = $sessions;
        $data['title'] = lang( 'sessions' );
        $data['view'] = 'sessions';
        
        $this->load_panel_template( $data );
    }
    
    /**
     * Activities Log Page
     *
     * @return void
     */
    public function activities_log()
    {
        if ( ! $this->zuser->has_permission( 'users' ) )
        {
            no_permission_redirect();
        }
        
        $this->set_admin_reference( 'tools' );
        
        $ip_address = do_secure( get( 'ip_address' ) );
        $config['base_url'] = env_url( 'admin/tools/activities_log' );
        
        $config['total_rows'] = $this->Tool_model->activities_log([
          'ip_address' => $ip_address,
          'count' => true
        ]);
        
        $config['per_page'] = PER_PAGE_RESULTS_PANEL;
        $offset = get_offset( $config['per_page'] );
        
        $this->pagination->initialize( $config );
        
        $data['data']['pagination'] = $this->pagination->create_links();
        
        $activities_log = $this->Tool_model->activities_log([
            'ip_address' => $ip_address,
            'limit' => $config['per_page'],
            'offset' => $offset
        ]);
        
        $data['data']['activities'] = $activities_log;
        $data['title'] = lang( 'activities_log' );
        $data['view'] = 'activities_log';
        
        $this->load_panel_template( $data );
    }
    
    /**
     * Backup Page
     *
     * @return void
     */
    public function backup()
    {
        if ( ! $this->zuser->has_permission( 'backup' ) )
        {
            no_permission_redirect();
        }
        
        $this->load->helper( 'z_backup' );
        
        $this->set_admin_reference( 'tools' );
        
        $config['base_url'] = env_url( 'admin/tools/backup' );
        $config['total_rows'] = $this->Tool_model->backup_log( true );
        $config['per_page'] = PER_PAGE_RESULTS_PANEL;
        $offset = get_offset( $config['per_page'] );
        
        $this->pagination->initialize( $config );
        $data['data']['pagination'] = $this->pagination->create_links();
        
        $backup_log = $this->Tool_model->backup_log(
            false,
            $config['per_page'],
            $offset
        );
        
        $data['data']['backup_log'] = $backup_log;
        $data['delete_method'] = 'delete_a_backup_log';
        $data['title'] = lang( 'backup' );
        $data['view'] = 'backup';
        
        $this->load_panel_template( $data );
    }
}
