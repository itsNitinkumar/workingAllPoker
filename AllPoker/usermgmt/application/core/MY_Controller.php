<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * MY Controller ( The Base Controller ).
 *
 * @author Shahzaib
 */
class MY_Controller extends CI_Controller {
    
    /**
     * Main Directory Inside "views/some_theme/".
     *
     * @var string
     */
    protected $area;
    
    /**
     * Sub Directory of Main Directory Inside "views/some_theme/area".
     *
     * @var string
     */
    protected $sub_area;
    
    /**
     * Parent Reference ( For Page Title )
     *
     * @var string
     */
    protected $parent_ref;
    
    /**
     * PHP 8.0+ Support
     *
     * @version 2.2
     */
    public $Dashboard_model;
    public $Email_token_model;
    public $Home_model;
    public $Login_model;
    public $Page_model;
    public $Payment_model;
    public $Setting_model;
    public $Subscriber_model;
    public $Support_model;
    public $Tool_model;
    public $User_model;
    public $session;
    public $benchmark;
    public $config;
    public $log;
    public $hooks;
    public $utf8;
    public $uri;
    public $router;
    public $output;
    public $security;
    public $input;
    public $lang;
    public $db;
    public $load;
    public $pagination;
    public $form_validation;
    public $upload;
    public $email;
    public $zmailer;
    public $zfiles;
    public $zpdf;
    public $zfacebook;
    public $ztwitter;
    public $zgoogle;
    public $zstripe;
    public $zuser;
    public $agent;
    public $zvkontakte;
    
    
    /**
     * Load Template
     *
     * Use to load the view ( inside "views/some_theme/dir(s)" ) with common
     * parts ( e.g. header ), title option, and page data.
     *
     * @param  array $options
     * @return void
     */
    protected function load_template( array $options )
    {
        if ( empty( $this->area ) ) exit( 'Missing Area Reference' );
        
        $area = $this->area;
        
        if ( ! empty( $options['area'] ) ) $area = $options['area'];
        
        // Page Header Common Data:
        if ( empty( $options['meta_description'] ) ) $options['meta_description'] = '';
        if ( empty( $options['meta_keywords'] ) ) $options['meta_keywords'] = '';

        if ( ! empty( $options['title'] ) && ! empty( $this->parent_ref ) )
        {
            $options['title'] = "{$this->parent_ref} › {$options['title']}";
        }
        else if ( ! empty( $this->parent_ref ) ) $options['title'] = $this->parent_ref;
        else if ( empty( $options['title'] ) ) $options['title'] = '';
        
        $common_area = $area;
        
        if ( ! empty( $options['common_area'] ) ) $common_area = $options['common_area'];
        
        $this->load->view( get_theme_name() . "common/{$common_area}/header", [
            'page_meta_description' => $options['meta_description'],
            'page_meta_keywords' => $options['meta_keywords'],
            'page_title' => $options['title']
        ]);
        
        if ( ! empty( $options['delete_method'] ) )
        {
            $options['data']['delete_method'] = $options['delete_method'];
        }
        
        $options['data']['area'] = $area;
        
        $this->load->view( get_theme_name() . "{$area}/{$options['view']}", $options['data'] );
        
        $this->load->view( get_theme_name() . "common/{$common_area}/footer", $options['data'] );
    }
    
    /**
     * Load Sub Tempate.
     *
     * Use to load the view ( with the general common parts e.g. header ) for the sub page e.g.
     * views/some_theme/admin/settings/"general".
     *
     * @param  array $options
     * @return void
     */
    protected function load_sub_template( array $options )
    {
        if ( empty( $options['view'] ) ) exit( 'Missing View' );
        
        $sub_area = $this->sub_area;
            
        if ( ! empty( $options['sub_area'] ) ) $sub_area = $options['sub_area'];
        
        if ( empty( $sub_area ) ) exit( 'Missing Sub Directory Reference' );
        
        $options['view'] = $sub_area . '/' . $options['view'];
        
        $this->load_template( $options );
    }
    
    /**
     * Set Admin Panel Reference
     *
     * Use to append the admin panel label with the passed key.
     *
     * @param  string $key Language file key
     * @return void
     */
    protected function set_admin_reference( $key )
    {
        $this->parent_ref = lang( 'admin_panel' ) . ' › ' . lang( $key );
    }
    
    /**
     * Set User Panel Reference
     *
     * Use to append the user panel label with the passed key.
     *
     * @param  string $key Language file key
     * @return void
     */
    protected function set_user_reference( $key )
    {
        $this->parent_ref = lang( 'user_panel' ) . ' › ' . lang( $key );
    }
    
    /**
     * Get Panel Area Parent(s) Reference
     *
     * @return string
     */
    protected function get_panel_parents_ref()
    {
        return $this->parent_ref;
    }
    
    /**
     * Load Panel Template
     *
     * Use to load the panel main or sub template with the general common parts e.g. header
     * Common files are located inside "views/some_theme/common/panel"
     *
     * Compatible with both parent and child pages.
     *
     * @param  array   $options
     * @param  boolean $is_sub
     * @return void
     */
    protected function load_panel_template( array $options, $is_sub = true )
    {
        $options['common_area'] = 'panel';
        
        if ( $is_sub === true )
        {
            $this->load_sub_template( $options );
        }
        else
        {
            $this->load_template( $options );
        }
    }
}