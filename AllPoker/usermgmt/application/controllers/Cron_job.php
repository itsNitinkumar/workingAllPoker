<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Cron Job Controller
 *
 * @author Shahzaib
 */
class Cron_job extends MY_Controller {
    
    /**
     * Regular
     *
     * @return void
     */
    public function regular()
    {
        $this->load->model( 'User_model' );
        $this->User_model->set_awayed_offline();
    }
}
