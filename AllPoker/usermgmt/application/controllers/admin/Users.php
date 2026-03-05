<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Users Controller ( Admin )
 *
 * @author Shahzaib
 */
class Users extends MY_Controller {

    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        if ( ! $this->zuser->is_logged_in ) env_redirect( 'login' );
        
        if ( ! $this->zuser->has_permission( 'users' ) )
        {
            no_permission_redirect();
        }
        
        $this->sub_area = 'users';
        $this->area = 'admin';
        
        $this->load->model( 'User_model' );
        $this->load->library( 'pagination' );
    }
    
    /**
     * New User Page
     *
     * @return void
     */
    public function new_user()
    {
        $this->set_admin_reference( 'users' );
        
        $data['data']['roles'] = $this->Setting_model->roles();
        $data['data']['fields'] = $this->Tool_model->custom_fields( 'ASC', true );
        $data['data']['reg_main'] = false;
        $data['title'] = lang( 'new_user' );
        $data['view'] = 'new_user';
        
        $this->load_panel_template( $data );
    }
    
    /**
     * Users Invites Page
     *
     * @return void
     */
    public function invites()
    {
        $this->set_admin_reference( 'users' );
        
        $config['base_url'] = env_url( 'admin/users/invites' );
        $config['total_rows'] = $this->User_model->invites( ['count' => true] );
        $config['per_page'] = PER_PAGE_RESULTS_PANEL;
        $offset = get_offset( $config['per_page'] );
        
        $this->pagination->initialize( $config );
        
        $data['data']['pagination'] = $this->pagination->create_links();
        
        $invites = $this->User_model->invites([
            'limit' => $config['per_page'],
            'offset' => $offset
        ]);
        
        $data['data']['invites'] = $invites;
        $data['delete_method'] = 'delete_invitation';
        $data['title'] = lang( 'invites' );
        $data['view'] = 'invites';
        
        $this->load_panel_template( $data );
    }
    
    /**
     * User Sent Emails Page
     *
     * @param   string $username
     * @return  void
     * @version 1.5
     */
    public function sent_emails( $username = '' )
    {
        $this->set_admin_reference( 'users' );
        
        $username = do_secure_l( $username );
        $user = $this->User_model->get_by_username( $username );
        
        if ( empty( $user ) ) env_redirect( 'admin/users/manage' );
        
        $config['base_url'] = env_url( "admin/users/sent_emails/{$username}" );
        
        $config['total_rows'] = $this->User_model->sent_emails([
            'user_id' => $user->id,
            'count' => true
        ]);
        
        $config['per_page'] = PER_PAGE_RESULTS_PANEL;
        $offset = get_offset( $config['per_page'], 5 );
        
        $this->pagination->initialize( $config );
        $data['data']['pagination'] = $this->pagination->create_links();
        
        $sent_emails = $this->User_model->sent_emails([
            'user_id' => $user->id,
            'limit' => $config['per_page'],
            'offset' => $offset
        ]);

        $data['data']['sent_emails'] = $sent_emails;
        $data['data']['user_email_address'] = $user->email_address;
        $data['delete_method'] = 'delete_sent_email';
        $data['title'] = lang( 'sent_emails' );
        $data['view'] = 'sent_emails';
        
        $this->load_panel_template( $data );
    }
    
    /**
     * User Activities Log Page
     *
     * @param  string $username
     * @return void
     */
    public function activities_log( $username = '' )
    {
        $this->set_admin_reference( 'users' );

        $username = do_secure_l( $username );
        $user = $this->User_model->get_by_username( $username );
        
        if ( empty( $user ) ) env_redirect( 'admin/users/manage' );
        
        $ip_address = do_secure( get( 'ip_address' ) );
        $config['base_url'] = env_url( "admin/users/activities_log/{$username}" );

        $config['total_rows'] = $this->Tool_model->activities_log([
            'ip_address' => $ip_address,
            'user_id' => $user->id,
            'count' => true
        ]);

        $config['per_page'] = PER_PAGE_RESULTS_PANEL;
        $offset = get_offset( $config['per_page'], 5 );
        
        $this->pagination->initialize( $config );
        $data['data']['pagination'] = $this->pagination->create_links();
        
        $activities_log = $this->Tool_model->activities_log([
            'ip_address' => $ip_address,
            'user_id' => $user->id,
            'limit' => $config['per_page'],
            'offset' => $offset
        ]);

        $data['data']['activities'] = $activities_log;
        $data['data']['user_email_address'] = $user->email_address;
        $data['title'] = lang( 'activities_log' );
        $data['view'] = 'activities_log';
        
        $this->load_panel_template( $data );
    }
    
    /**
     * User Sessions Page
     *
     * @param  string $username
     * @return void
     */
    public function sessions( $username = '' )
    {
        $this->set_admin_reference( 'users' );
        
        $username = do_secure_l( $username );
        $user = $this->User_model->get_by_username( $username );
        
        if ( empty( $user ) ) env_redirect( 'admin/users/manage' );
        
        $config['base_url'] = env_url( "admin/users/sessions/{$username}" );
        
        $config['total_rows'] = $this->Tool_model->user_sessions([
            'user_id' => $user->id,
            'count' => true
        ]);
        
        $config['per_page'] = PER_PAGE_RESULTS_PANEL;
        $offset = get_offset( $config['per_page'], 5 );
        
        $this->pagination->initialize( $config );
        $data['data']['pagination'] = $this->pagination->create_links();
        
        $sessions = $this->Tool_model->user_sessions([
            'user_id' => $user->id,
            'limit' => $config['per_page'],
            'offset' => $offset
        ]);
        
        $data['data']['count'] = $config['total_rows'];
        $data['data']['sessions'] = $sessions;
        $data['data']['user_email_address'] = $user->email_address;
        $data['data']['username'] = $username;
        $data['title'] = lang( 'sessions' );
        $data['view'] = 'sessions';
        
        $this->load_panel_template( $data );
    }
    
    /**
     * Adjust Balance Page
     *
     * @param  integer $user_id
     * @return void
     */
    public function adjust_balance( $user_id = 0 )
    {
        if ( ! $this->zuser->has_permission( 'payment' ) )
        {
            no_permission_redirect();
        }
        
        $user_id = intval( $user_id );
        $user = $this->User_model->get_by_id( $user_id );
        
        if ( empty( $user ) ) env_redirect( 'admin/users/manage' );
        
        $this->set_admin_reference( 'users' );
        
        $data['data']['email_address'] = $user->email_address;
        $data['data']['user_id'] = $user_id;
        $data['data']['credits'] = $this->User_model->credits( $user_id );
        $data['title'] = lang( 'adjust_balance' );
        $data['view'] = 'adjust_balance';
        
        $this->load_panel_template( $data );
    }
    
    /**
     * Manage Users Page
     *
     * @return void
     */
    public function manage()
    {
        $this->set_admin_reference( 'users' );
        
        $searched = do_secure( get( 'search' ) );
        $filter = do_secure( get( 'filter' ) );
        $role = intval( get( 'role' ) );
        $config['base_url'] = env_url( 'admin/users/manage' );
        
        $config['total_rows'] = $this->User_model->users([
            'filter' => $filter,
            'searched' => $searched,
            'role' => $role,
            'count' => true
        ]);
        
        $config['per_page'] = PER_PAGE_RESULTS_PANEL;
        $offset = get_offset( $config['per_page'] );
        
        $this->pagination->initialize( $config );
        $data['data']['pagination'] = $this->pagination->create_links();
        
        $data['data']['users'] = $this->User_model->users([
            'filter' => $filter,
            'searched' => $searched,
            'role' => $role,
            'limit' => $config['per_page'],
            'offset' => $offset
        ]);
        
        $data['data']['roles'] = $this->Setting_model->roles();
        $data['delete_method'] = 'delete_user';
        $data['title'] = lang( 'manage' );
        $data['view'] = 'manage';
        
        $this->load_panel_template( $data );
    }
    
    /**
     * Edit User Page
     *
     * @param  integer $user_id
     * @return void
     */
    public function edit_user( $user_id = 0 )
    {
        $user_id = intval( $user_id );
        $user = $this->User_model->get_by_id( $user_id );
        
        if ( empty( $user ) ) env_redirect( 'admin/users/manage' );
        
        $this->set_admin_reference( 'users' );
        
        $data['title'] = sub_title( lang( 'manage' ), lang( 'edit_user' ) );
        $data['data']['credits'] = $this->User_model->credits( $user_id );
        $data['data']['roles'] = $this->Setting_model->roles();
        $data['data']['fields'] = $this->User_model->cf_data( $user_id );
        $data['data']['user'] = $user;
        $data['view'] = 'edit_user';
        
        $this->load_panel_template( $data );
    }
}
