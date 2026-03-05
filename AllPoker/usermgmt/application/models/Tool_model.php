<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Tool Model
 *
 * @author Shahzaib
 */
class Tool_model extends MY_Model {
    
    /**
     * User Session by Token
     *
     * @param   string $token
     * @return  object
     * @version 1.6
     */
    public function user_session_by_token( $token )
    {
        $data['where']['token'] = $token;
        $data['table'] = 'users_sessions';
        
        return $this->get_one( $data );
    }
    
    /**
     * Delete User Session by Token
     *
     * @param   string $token
     * @return  boolean
     * @version 1.6
     */
    public function delete_user_session_by_token( $token )
    {
        $data['column'] = 'token';
        $data['column_value'] = $token;
        $data['table'] = 'users_sessions';
        
        return $this->delete( $data );
    }
    
    /**
     * User Sessions
     *
     * @param  array $options
     * @return mixed
     */
    public function user_sessions( array $options = [] )
    {
        $data['table'] = 'users_sessions';
        
        if ( empty( $options['user_id'] ) )
        {
            $data['select'] = 'users_sessions.*, users.first_name, users.last_name';
            $data['join'] = ['table' => 'users', 'on' => 'users.id = users_sessions.user_id'];
        }
        else
        {
            $data['where'] = ['user_id' => $options['user_id']];
        }
        
        if ( ! empty( $options['limit'] ) ) $data['limit'] = $options['limit'];
        
        if ( ! empty( $options['offset'] ) ) $data['offset'] = $options['offset'];
        
        if ( @$options['count'] === true )
        {
            return $this->get_count( $data );
        }
        
        return $this->get( $data );
    }
    
    /**
     * User ID by Session Token ( Logged in User ).
     *
     * @return  integer
     * @version 1.5
     */
    public function user_id_by_sess_token()
    {
        $data['where']['token'] = get_session( USER_TOKEN );
        $data['table'] = 'users_sessions';

        $row = $this->get_one( $data );
        
        if ( ! empty( $row->user_id ) )
        return $row->user_id;
    }
    
    /**
     * User Session
     *
     * @param  integer $id
     * @param  integer $user_id
     * @return object
     */
    public function user_session( $id, $user_id = 0 )
    {
        $data['where']['id'] = $id;
        $data['table'] = 'users_sessions';
        
        if ( ! empty( $user_id ) )
        {
            $data['where']['user_id'] = $user_id;
        }
        
        return $this->get_one( $data );
    }
    
    /**
     * Delete User Session
     *
     * @param  integer $id
     * @return boolean
     */
    public function delete_user_session( $id )
    {
        $data['column_value'] = $id;
        $data['table'] = 'users_sessions';
        
        return $this->delete( $data );
    }
    
    /**
     * Logout My Other(s)
     *
     * @return boolean
     */
    public function logout_my_others()
    {
        $user_id = $this->zuser->get( 'id' );
        $current_token = get_session( USER_TOKEN );
        
        $data['where'] = ['user_id' => $user_id, 'token !=' => $current_token];
        $data['table'] = 'users_sessions';
        
        return $this->delete( $data );
    }
    
    /**
     * Delete User Sessions
     *
     * @param  integer $user_id
     * @return boolean
     */
    public function delete_user_sessions( $user_id )
    {
        $data['where']['user_id'] = $user_id;
        $data['table'] = 'users_sessions';
        
        return $this->delete( $data );
    }
    
    /**
     * Announcements
     *
     * @param  boolean $count
     * @param  integer $limit
     * @param  integer $offset
     * @return mixed
     */
    public function announcements( $count = false, $limit = 0, $offset = 0 )
    {
        $data['table'] = 'announcements';
        $data['limit'] = $limit;
        $data['offset'] = $offset;
        
        if ( $count === true )
        {
            return $this->get_count( $data );
        }
        
        return $this->get( $data );
    }
    
    /**
     * Announcement
     *
     * @param  integer $id
     * @return object
     */
    public function announcement( $id )
    {
        $data['column_value'] = $id;
        $data['table'] = 'announcements';
        
        return $this->get_one( $data );
    }
    
    /**
     * Add Announcement
     *
     * @param  array $data
     * @return mixed
     */
    public function add_announcement( $data )
    {
        return $this->add( $data, 'announcements' );
    }
    
    /**
     * Update Announcement
     *
     * @param  array   $to_update
     * @param  integer $id
     * @return boolean
     */
    public function update_announcement( $to_update, $id )
    {
       $data['column_value'] = $id;
       $data['table'] = 'announcements';
       $data['data'] = $to_update;
       
       return $this->update( $data );
    }
    
    /**
     * Check for New Announcements
     *
     * @return integer
     */
    public function check_for_new_announcements()
    {
        $user_last_read = $this->zuser->get( 'announcements_last_read_at' );
        $data['where'] = ['created_at >=' => intval( $user_last_read )];
        $data['table'] = 'announcements';
            
        return $this->get_count( $data );
    }
    
    /**
     * Mark Announcements as Read
     *
     * @param  integer $user_id
     * @return void
     */
    public function mark_announcements_as_read( $user_id )
    {
       $data['column_value'] = $user_id;
       $data['data'] = ['announcements_last_read_at' => time()];
       $data['table'] = 'users';
       
       $this->update( $data );
    }
    
    /**
     * Delete Announcement
     *
     * @param  integer $id
     * @return boolean
     */
    public function delete_announcement( $id )
    {
        $data['column_value'] = $id;
        $data['table'] = 'announcements';
        
        return $this->delete( $data );
    }
    
    /**
     * Custom Fields
     *
     * @param  string  $order
     * @param  boolean $or
     * @return object
     */
    public function custom_fields( $order = 'DESC', $or = false )
    {
        $data['table'] = 'custom_fields';
        $data['order'] = $order;
        
        if ( $or === true )
        {
            $data['where'] = ['on_registeration' => 1];
        }
        
        return $this->get( $data );
    }
    
    /**
     * Custom Field
     *
     * @param  integer $id
     * @return object
     */
    public function custom_field( $id )
    {
        $data['column_value'] = $id;
        $data['table'] = 'custom_fields';
        
        return $this->get_one( $data );
    }
    
    /**
     * Add Custom Field
     *
     * @param  array $data
     * @return mixed
     */
    public function add_custom_field( $data )
    {
        return $this->add( $data, 'custom_fields' );
    }
    
    /**
     * Update Custom Field
     *
     * @param  array   $to_update
     * @param  integer $id
     * @return boolean
     */
    public function update_custom_field( $to_update, $id )
    {
        $data['column_value'] = $id;
        $data['table'] = 'custom_fields';
        $data['data'] = $to_update;

        return $this->update( $data );
    }
    
    /**
     * Delete User Custom Fields
     *
     * @param  integer $user_id
     * @return void
     */
    public function delete_user_custom_fields( $user_id )
    {
        $data['where']['user_id'] = $user_id;
        $data['table'] = 'users_custom_fields';
        
        $this->delete( $data );
    }
    
    /**
     * Delete Users Custom Fields by ID.
     *
     * @param  integer $cf_id Custom Field ID
     * @return void
     */
    public function delete_ucf_by_id( $cf_id )
    {
        $data['where']['custom_field_id'] = $cf_id;
        $data['table'] = 'users_custom_fields';
        
        $this->delete( $data );
    }
    
    /**
     * Delete Custom Field
     *
     * @param  integer $id
     * @return boolean
     */
    public function delete_custom_field( $id )
    {
        $data['column_value'] = $id;
        $data['table'] = 'custom_fields';
        
        return $this->delete( $data );
    }
    
    /**
     * Email Templates
     *
     * @return object
     */
    public function email_templates()
    {
        $data['table'] = 'email_templates';
        $data['order'] = 'ASC';
        
        return $this->get( $data );
    }
    
    /**
     * Email Template
     *
     * @param  integer $id
     * @return object
     */
    public function email_template( $id )
    {
        $data['column_value'] = $id;
        $data['table'] = 'email_templates';
        
        return $this->get_one( $data );
    }
    
    /**
     * Email Template by Hook Hook and Language.
     *
     * @param  string $hook
     * @param  string $lang_key
     * @return object
     */
    public function email_template_by_hook_and_lang( $hook, $lang_key )
    {
        $data['where'] = ['hook' => $hook, 'language' => $lang_key];
        $data['table'] = 'email_templates';
        
        return $this->get_one( $data );
    }
    
    /**
     * Add Email Template
     *
     * @param  array $data
     * @return mixed
     */
    public function add_email_template( $data )
    {
        return $this->add( $data, 'email_templates' );
    }
    
    /**
     * Update Email Template
     *
     * @param  array   $to_update
     * @param  integer $id
     * @return boolean
     */
    public function update_email_template( $to_update, $id )
    {
       $data['column_value'] = $id;
       $data['table'] = 'email_templates';
       $data['data'] = $to_update;
       
       return $this->update( $data );
    }
    
    /**
     * Delete Email Template
     *
     * @param  integer $id
     * @return boolean
     */
    public function delete_email_template( $id )
    { 
        $data['column_value'] = $id;
        $data['table'] = 'email_templates';
        
        return $this->delete( $data );
    }
    
    /**
     * Blocked IP Addresses
     *
     * @param  boolean $count
     * @param  integer $limit
     * @param  integer $offset
     * @return mixed
     */
    public function blocked_ip_addresses( $count = false, $limit = 0, $offset = 0 )
    {
        $data['table'] = 'blocked_ip_addresses';
        $data['limit'] = $limit;
        $data['offset'] = $offset;
        
        if ( $count === true )
        {
            return $this->get_count( $data );
        }
        
        return $this->get( $data );
    }
    
    /**
     * Blocked IP Address
     *
     * @param  mixed  $value
     * @param  string $column
     * @return object
     */
    public function blocked_ip_address( $value, $column = 'ip_address' )
    {
        $data['column'] = $column;
        $data['column_value'] = $value;
        $data['table'] = 'blocked_ip_addresses';
        
        return $this->get_one( $data );
    }
    
    /**
     * Block IP Address
     *
     * @param  array $data
     * @return mixed
     */
    public function block_ip_address( $data )
    {
        return $this->add( $data, 'blocked_ip_addresses' );
    }
    
    /**
     * Delete IP Address
     *
     * @param  integer $id
     * @return boolean
     */
    public function delete_ip_address( $id )
    {
        $data['column_value'] = $id;
        $data['table'] = 'blocked_ip_addresses';
        
        return $this->delete( $data );
    }
    
    /**
     * Activities Log ( Users )
     *
     * @param  array $options
     * @return mixed
     */
    public function activities_log( array $options = [] )
    {
        $data['table'] = 'activities_log';
        
        if ( empty( $options['user_id'] ) )
        {
            $data['select'] = 'activities_log.*, users.first_name, users.last_name';
            $data['join'] = ['table' => 'users', 'on' => 'users.id = activities_log.user_id'];
        }
        else
        {
            $data['where'] = ['user_id' => $options['user_id']];
        }
        
        if ( ! empty( $options['ip_address'] ) )
        {
            $data['where'] = ['ip_address' => $options['ip_address']];
        }
        
        if ( ! empty( $options['limit'] ) ) $data['limit'] = $options['limit'];
        
        if ( ! empty( $options['offset'] ) ) $data['offset'] = $options['offset'];
        
        if ( @$options['count'] === true )
        {
            return $this->get_count( $data );
        }
        
        return $this->get( $data );
    }
    
    /**
     * Log User Activity
     *
     * @param  array $data
     * @return void
     */
    public function log_user_activity( $data )
    {
        $this->add( $data, 'activities_log' );
    }
    
    /**
     * Clear User Log
     *
     * @param  integer $user_id
     * @return void
     */
    function clear_user_log( $user_id )
    {
        $data['where']['user_id'] = $user_id;
        $data['table'] = 'activities_log';
        
        $this->delete( $data );
    }
    
    /**
     * Clear Log ( Users )
     *
     * @param  integer $period
     * @return boolean
     */
    public function clear_log( $period )
    {
        $status = null;
        
        if ( $period === 1 ) $period = subtract_time( '3 days' );
        else if ( $period === 2 ) $period = subtract_time( '7 days' );
        else if ( $period === 3 ) $period = subtract_time( '14 days' );
        else if ( $period === 4 ) $period = subtract_time( '1 month' );
        else if ( $period === 5 ) $period = subtract_time( '3 months' );
        else if ( $period === 6 ) $period = subtract_time( '6 months' );
        else if ( $period === 7 ) $period = subtract_time( '12 months' );
        else $period = 0;
        
        if ( empty( $period ) )
        {
            $status = $this->delete_all( 'activities_log' );
        }
        else
        {
            $data['column'] = 'performed_at <';
            $data['column_value'] = $period;
            $data['table'] = 'activities_log';

            $status = $this->delete( $data );
        }
        
        return $status;
    }
    
    /**
     * Backup Log
     *
     * @param  boolean $count
     * @param  integer $limit
     * @param  integer $offset
     * @return mixed
     */
    public function backup_log( $count = false, $limit = 0, $offset = 0 )
    {
        $data['table'] = 'backup_log';
        $data['limit'] = $limit;
        $data['offset'] = $offset;
        
        if ( $count === true )
        {
            return $this->get_count( $data );
        }
        
        return $this->get( $data );
    }
    
    /**
     * Log a Backup
     *
     * @param  array $data
     * @return mixed
     */
    public function log_a_backup( $data )
    {
        return $this->add( $data, 'backup_log' );
    }
    
    /**
     * Delete a Backup Log
     *
     * @param  integer $id
     * @return boolean
     */
    public function delete_a_backup_log( $id )
    {
        $data['column_value'] = $id;
        $data['table'] = 'backup_log';
        
        return $this->delete( $data );
    }
}
