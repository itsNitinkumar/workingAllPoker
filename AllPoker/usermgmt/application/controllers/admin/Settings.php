<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Settings Controller ( Admin )
 *
 * @author Shahzaib
 */
class Settings extends MY_Controller {
    
    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        if ( ! $this->zuser->is_logged_in ) env_redirect( 'login' );
        
        if ( ! $this->zuser->has_permission( 'settings' ) )
        {
            no_permission_redirect();
        }
        
        $this->sub_area = 'settings';
        $this->area = 'admin';
    }
    
    /**
     * General Settings Page
     *
     * @return void
     */
    public function general()
    {
        $this->set_admin_reference( 'settings' );
        
        $data['title'] = lang( 'general' );
        $data['view'] = 'general';
        
        $this->load_panel_template( $data );
    }
    
    /**
     * Support Settings Page
     *
     * @return void
     */
    public function support()
    {
        $this->set_admin_reference( 'settings' );
        
        $data['title'] = lang( 'support' );
        $data['view'] = 'support';
        
        $this->load_panel_template( $data );
    }
    
    /**
     * Roles Setting Page
     *
     * @param  integer $id
     * @return void
     */
    public function roles( $id = 0 )
    {
        if ( ! $this->zuser->has_permission( 'roles_and_permissions' ) )
        {
            no_permission_redirect();
        }
        
        $this->set_admin_reference( 'settings' );
        
        if ( ! empty( $id ) )
        {
            $role = $this->Setting_model->role( intval( $id ) );
            
            if ( ! empty( $role ) )
            {
                $data['data']['permissions'] = $this->Setting_model->permissions();
                $data['data']['role'] = $role->name;
                $data['data']['role_id'] = $role->id;
                $data['title'] = sub_title( lang( 'roles' ), lang( 'permissions' ) );
                $data['view'] = 'roles_permissions';
            }
            else
            {
                env_redirect( 'admin/settings/roles' );
            }
        }
        else
        {
            $data['data']['roles'] = $this->Setting_model->roles();
            $data['delete_method'] = 'delete_role';
            $data['title'] = lang( 'roles' );
            $data['view'] = 'roles';
        }
        
        $this->load_panel_template( $data );
    }
    
    /**
     * Permissions Setting Page
     *
     * @return void
     */
    public function permissions()
    {
        if ( ! $this->zuser->has_permission( 'roles_and_permissions' ) )
        {
            no_permission_redirect();
        }
        
        $this->set_admin_reference( 'settings' );
        
        $data['data']['permissions'] = $this->Setting_model->permissions();
        $data['delete_method'] = 'delete_permission';
        $data['title'] = lang( 'permissions' );
        $data['view'] = 'permissions';
        
        $this->load_panel_template( $data );
    }
    
    /**
     * Users Settings Page
     *
     * @return void
     */
    public function users()
    {
        $this->set_admin_reference( 'settings' );
        
        $data['data']['roles'] = $this->Setting_model->roles();
        $data['title'] = lang( 'users' );
        $data['view'] = 'users';
        
        $this->load_panel_template( $data );
    }
    
    /**
     * Payment Settings Page
     *
     * @return void
     */
    public function payment()
    {
        $this->set_admin_reference( 'settings' );
        
        $data['title'] = lang( 'payment' );
        $data['view'] = 'payment';
        
        $this->load_panel_template( $data );
    }
    
    /**
     * APIs Settings Page
     *
     * @return void
     */
    public function apis()
    {
        $this->set_admin_reference( 'settings' );
        
        $data['title'] = lang( 'apis' );
        $data['view'] = 'apis';
        
        $this->load_panel_template( $data );
    }
    
    /**
     * Email Settings Page
     *
     * @return void
     */
    public function email()
    {
        $this->set_admin_reference( 'settings' );
        
        $data['title'] = lang( 'email' );
        $data['view'] = 'email';
        
        $this->load_panel_template( $data );
    }
}
