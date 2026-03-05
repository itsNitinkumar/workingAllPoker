<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * MY Loader ( The Base Loader ).
 *
 * @author Shahzaib
 */
class MY_Loader extends CI_Loader {
    
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
}
