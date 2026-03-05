<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Request Helper
 *
 * @author Shahzaib
 */


/**
 * Is Blocked Requester ( IP Address ).
 *
 * @return boolean
 */
if ( ! function_exists( 'is_blocked_requester' ) )
{
    function is_blocked_requester()
    {
        $ci =& get_instance();
        
        if ( $ci->Tool_model->blocked_ip_address( $ci->input->ip_address() ) )
        {
            return true;
        }
        
        return false;
    }
}

/**
 * Request
 *
 * Use to manage the form request.
 *
 * @param  string $type e.g. post
 * @param  string $key
 * @return string|null
 */
if ( ! function_exists( 'request' ) )
{
    function request( $type, $key )
    {
        $ci =& get_instance();
        return $ci->input->{$type}( $key );
    }
}

/**
 * Post
 *
 * Use to manage the POST request.
 *
 * @param  string $key
 * @return string|null
 */
if ( ! function_exists( 'post' ) )
{
    function post( $key )
    {
        return request( 'post', $key );
    }
}

/**
 * Get
 *
 * Use to manage the GET request.
 *
 * @param  string $key
 * @return string|null
 */
if ( ! function_exists( 'get' ) )
{
    function get( $key )
    {
        return request( 'get', $key );
    }
}

/**
 * Get Managed Swith
 *
 * POST request handling for the switch input.
 *
 * @param  string $key
 * @return integer
 */
if ( ! function_exists( 'get_managed_switch' ) )
{
    function get_managed_switch( $key )
    {
        if ( post( $key ) )
        {
            return 1;
        }
        
        return 0;
    }
}
