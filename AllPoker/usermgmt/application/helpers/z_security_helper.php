<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Security Helper
 *
 * @author Shahzaib
 */


/**
 * Only Binary ( 0, 1 ) Number
 *
 * Use to avoid the other numbers in case there
 * is a requirement for input to be 0 or 1.
 *
 * @param   integer $number
 * @return  integer
 * @version 1.9
 */
if ( ! function_exists( 'only_binary' ) )
{
    function only_binary( $number )
    {
        return ( $number == 1 ) ? 1 : 0;
    }
}

/**
 * HTML Esc URL
 *
 * Use to decode the URL and escape HTML.
 *
 * @param  string $enc_url
 * @return string
 */
if ( ! function_exists( 'html_esc_url' ) )
{
    function html_esc_url( $enc_url )
    {
        return html_escape( urldecode( $enc_url ) );
    }
}

/**
 * Stripe Extra HTML
 *
 * @param  string $text
 * @return string
 */
if ( ! function_exists( 'strip_extra_html' ) )
{
    function strip_extra_html( $text )
    {
        // Text Editor Allowed Tags:
        $tags = '<p><span><blockquote><h1><h2><h3><h4><h5><h6>';
        $tags .= '<br><b><u><pre><ul><ol><li><table><thead><tbody>';
        $tags .= '<tr><th><td><a><img><iframe><div>';
        
        return strip_tags( $text, $tags );
    }
}

/**
 * Do Secure
 *
 * Use to avoid the XSS attack & sanitize the string.
 *
 * @param  string  $string
 * @param  boolean $multi_spaces
 * @return string
 */
if ( ! function_exists( 'do_secure' ) )
{
    function do_secure( $string, $multi_spaces = false )
    {
        if ( empty( $string ) ) return '';
        
        $ci =& get_instance();
        
        $string = $ci->security->xss_clean( $string );
        $string = stripslashes( $string );
        
        if ( $multi_spaces === false )
        $string = preg_replace( '/\s+/', ' ', $string );
        
        $string = trim( $string );
        
        return $string;
    }
}

/**
 * Secured Lowercase String
 *
 * @param  string $string
 * @return string
 */
if ( ! function_exists( 'do_secure_l' ) )
{
    function do_secure_l( $string )
    {
        return strtolower( do_secure( $string ) );
    }
}

/**
 * Secured Uppercase String
 *
 * @param  string $string
 * @return string
 */
if ( ! function_exists( 'do_secure_u' ) )
{
    function do_secure_u( $string )
    {
        return ucfirst( do_secure( $string ) );
    }
}

/**
 * Do Secure the URL
 *
 * Use to make secured, and encode the URL string.
 *
 * @param  string $url
 * @return string
 */
if ( ! function_exists( 'do_secure_url' ) )
{
    function do_secure_url( $url )
    {
        $url = do_secure( $url );
        
        return urlencode( $url );
    }
}
