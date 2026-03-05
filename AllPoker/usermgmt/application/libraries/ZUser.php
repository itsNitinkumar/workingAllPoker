<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * ZUser Library
 *
 * @author Shahzaib
 */
class ZUser {
    
    /**
     * CodeIgniter
     *
     * @var object
     */
    private $ci;
    
    /**
     * Permission Keys ( Based on Role )
     *
     * @var    array
     * @version 1.4
     */
    private $perm_keys = [];
    
    /**
     * Login Status
     *
     * @var boolean
     */
    public $is_logged_in = false;
    
    /**
     * Logged in User Data
     *
     * @var object
     */
    public $data;
    
    
    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->ci =& get_instance();
        $token = $this->get_token();
        $impersonating = get_session( 'impersonating' );
        $this->data = new stdClass();
        $user_id = 0;
        
        if ( ! $impersonating )
        {
            $this->ci->db->where( 'token', $token );
            $record = $this->ci->db->get( 'users_sessions' );
            
            if ( $record->num_rows() > 0 ) $user_id = $record->row()->user_id;
        }
        else
        {
            $user_id = get_session( 'user_id' );
        }
        
        $this->ci->db->where( 'id', $user_id );
        $record = $this->ci->db->get( 'users' );
        
        if ( $record->num_rows() > 0 ) $user = $record->row();
        
        if ( ! empty( $user_id ) && ! empty( $user ) )
        {
            $this->is_logged_in = true;
            $this->data = $user;
            
            $this->update_online_time();
            
            // Store token value in session again if the session is
            // expired but, the user's token is available in a cookie.
            if ( empty( get_session( USER_TOKEN ) ) )
            {
                set_session( USER_TOKEN, $token );
            }
            
            $this->update_last_activity();
            
            if ( $user->status == 0 && ! $impersonating )
            {
                set_flashdata( 'banned', true );
                $this->logout( 'login/banned', true );
            }
        }
        else if ( $impersonating )
        {
            // Deimpersonate the user if impersonating with the
            // account that is deleted on the running:
            $this->deimpersonate();
        }
        else if ( ! empty( $token ) )
        {
            // Clear token in case the user token is deleted from the
            // database but, still exists in session or cookie:
            $this->clear_token();
        }
        
        $this->set_perm_keys();
    }
    
    /**
     * Get Token
     *
     * @return string     
     */
    private function get_token()
    {
        $session = get_session( USER_TOKEN );
        $cookie = get_cookie( USER_TOKEN );
        
        return ( ! empty( $session ) ) ? $session : $cookie;
    }
    
    /**
     * Clear Token
     *
     * @return void
     */
    private function clear_token()
    {
        unset_session( USER_TOKEN );
        delete_cookie( USER_TOKEN );
    }
    
    /**
     * Update User Last Activity ( Session ).
     *
     * @return  void
     * @version 1.5
     */
    private function update_last_activity()
    {
        if ( ! get_session( 'impersonating' ) )
        {
            $this->ci->db->where( 'token', get_session( USER_TOKEN ) );
            $this->ci->db->set( 'last_activity', time() );
            $this->ci->db->set( 'last_location', uri_string() );
            $this->ci->db->update( 'users_sessions' );
            
            // Keep the last activity of the user in the main table
            // to avoid loosing it if the user session(s) is deleted:
            $this->ci->db->where( 'id', $this->get( 'id' ) );
            $this->ci->db->set( 'last_activity', time() );
            $this->ci->db->update( 'users' );
        }
    }
    
    /**
     * Update Online Time
     *
     * @return void
     */
    private function update_online_time()
    {
        // Update the user online status after every five minutes of the
        // last activity or the user is marked as offline while online:
        if ( ! get_session( 'impersonating' ) )
        if ( $this->data->online_time < ( time() - 60 * 5 ) || $this->data->is_online == 0 )
        {
            $this->ci->load->model( 'Setting_model' );
            $db_config = get_settings();
            $tz_support = $db_config['site_timezone'];
      
            $this->ci->db->where( 'id', $this->data->id );
            $this->ci->db->set( 'online_date', get_site_date( '', $tz_support ) );
            $this->ci->db->set( 'online_time', time() );
            $this->ci->db->set( 'is_online', 1 );
            $this->ci->db->update( 'users' );
        }
    }
    
    /**
     * Get ( Logged in User Data ).
     *
     * Use to get the non-custom fields data
     * of the logged in user.
     *
     * @param  string $property
     * @return string
     */
    public function get( $property )
    {
        if ( isset( $this->data->{$property} ) )
        {
            return $this->data->{$property};
        }
    }
    
    /**
     * Check Premium Time
     *
     * Use to verify the premium time of the
     * logged in user.
     *
     * @return boolean
     */
    public function check_premium_time()
    {
        $time = $this->get( 'premium_time' );
        
        if ( $time == -1 || $time > time() )
        {
            return true;
        }
        
        return false;
    }
    
    /**
     * Check Item
     *
     * Use to check the logged-in user has
     * purchased the specific item or not.
     *
     * @param  integer $item_id
     * @return boolean
     */
    public function check_item( $item_id )
    {
        if ( $this->get( 'premium_item_id' ) == $item_id )
        {
            return true;
        }
        
        return false;
    }
    
    /**
     * Impersonate
     *
     * @param  integer $id User ID
     * @return void
     */
    public function impersonate( $id )
    {
        set_session( 'impersonating', true );
        set_session( 'user_id', $id );
    }
    
    /**
     * Deimpersonate
     *
     * @return void
     */
    public function deimpersonate()
    {
        unset_session( 'impersonating' );
        unset_session( 'user_id' );
    }
    
    /**
     * Role
     *
     * Use to check that is the logged-in user
     * is assigned a specific role.
     *
     * @param  string $access_key
     * @return boolean
     */
    public function role( $access_key )
    {
        $this->ci->db->join( 'roles r', 'r.id = u.role');
        $this->ci->db->where( 'r.access_key', $access_key );
        $this->ci->db->where( 'u.id', $this->get( 'id' ) );
        $record = $this->ci->db->get( 'users u' );
        
        if ( $record->num_rows() > 0 )
        {
            return true;
        }
        
        return false;
    }
    
    /**
     * Set Permission Keys
     *
     * Use to assign the user allowed permissions to
     * the class property.
     *
     * @return  void
     * @version 1.4
     */
    private function set_perm_keys()
    {
        if ( $this->is_logged_in )
        {
            $this->ci->db->select( 'p.access_key' );
            $this->ci->db->from( 'roles_permissions rp' );
            $this->ci->db->join( 'permissions p', 'p.id = rp.permission_id' );
            $this->ci->db->where( 'rp.role_id', $this->data->role );
            
            $record = $this->ci->db->get();
            
            if ( $record->num_rows() > 0 )
            $this->perm_keys = $record->result_array();
        }
    }
    
    /**
     * Has Permission
     *
     * Use to check that is the logged in user has a
     * specific permission.
     *
     * @param  string $which
     * @return boolean
     */
    public function has_permission( $which )
    {
        $has = false;
        $key = array_search( $which, array_column( $this->perm_keys, 'access_key') );
        $has = ( $key !== false );
        
        return $has;
    }
    
    /**
     * Logout
     *
     * @param  string  $togo
     * @param  boolean $banned
     * @return void
     */
    public function logout( $togo = 'login', $banned = false )
    {
        if ( $this->is_logged_in )
        {
            $this->is_logged_in = false;
            
            log_user_activity( 'user_logged_out' );
            
            $this->ci->db->where( 'token', $this->get_token() );
            $this->ci->db->delete( 'users_sessions' );
            
            $this->ci->db->where( 'id', $this->data->id );
            $this->ci->db->set( 'is_online', 0 );
            $this->ci->db->update( 'users' );
            
            $this->deimpersonate();
            $this->clear_token();
        }
        
        if ( $banned && $this->ci->input->is_ajax_request() )
        {
            r_s_jump( $togo );
        }
        
        env_redirect( $togo );
    }
}
