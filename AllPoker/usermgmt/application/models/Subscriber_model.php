<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Subscriber ( Newsletter ) Model.
 *
 * @author Shahzaib
 */
class Subscriber_model extends MY_Model {
    
    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->table = 'subscribers';
    }
    
    /**
     * Confirmed Subscribers
     *
     * @return  object
     * @version 2.1
     */
    public function confirmed_subscribers()
    {
        $data['where'] = ['confirmed_at IS NOT NULL' => null];
        
        return $this->get( $data );
    }
    
    /**
     * Subscribers
     *
     * @return mixed
     */
    public function subscribers( $count = false, $limit = 0, $offset = 0 )
    {
        $data['limit'] = $limit;
        $data['offset'] = $offset;
        
        if ( $count === true )
        {
            return $this->get_count( $data );
        }
        
        return $this->get( $data );
    }
    
    /**
     * Subscriber
     *
     * @param  string $email_address
     * @return object
     */
    public function subscriber( $email_address )
    {
        $data['where'] = ['email_address' => $email_address];
        
        return $this->get_one( $data );
    }
    
    /**
     * Verify Unconfirmed Subscriber by Token.
     *
     * @param  string $token
     * @return object
     */
    public function verify_uc_by_token( $token )
    {
        $data['where'] = ['authentication_token' => $token, 'confirmed_at' => null];
        
        return $this->get_one( $data );
    }
    
    /**
     * Verify Subscriber by Token.
     *
     * @param  string $token
     * @return object
     */
    public function verify_by_token( $token )
    {
        $data['where'] = ['authentication_token' => $token];
        
        return $this->get_one( $data );
    }
    
    /**
     * Confirm the Subscriber
     *
     * @param  string $token
     * @return boolean
     */
    public function confirm( $token )
    {
        $data['column_value'] = $token;
        $data['column'] = 'authentication_token';
        $data['data'] = ['confirmed_at' => time()];

        return $this->update( $data );
    }
    
    /**
     * Delete Subscriber by Token
     *
     * @param  string $token
     * @return boolean
     */
    public function delete_by_token( $token )
    {
        $data['column_value'] = $token;
        $data['column'] = 'authentication_token';
        
        return $this->delete( $data );
    }
    
    /**
     * Delete Subscriber
     *
     * @param  integer $id
     * @return boolean
     */
    public function delete_subscriber( $id )
    {
        $data['column_value'] = $id;
        
        return $this->delete( $data );
    }
}
