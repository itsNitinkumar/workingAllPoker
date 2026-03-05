<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Dashboard Model
 *
 * @author Shahzaib
 */
class Dashboard_model extends MY_Model {
    
    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->table = 'dashboard';
    }
}
