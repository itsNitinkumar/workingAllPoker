<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Account Controller ( User )
 *
 * @author Shahzaib
 */
class Account extends MY_Controller {
    
    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        if ( ! $this->zuser->is_logged_in )
        {
            env_redirect( 'login' );
        }
        
        $this->sub_area = 'account';
        $this->area = 'user';
    }
    
    /**
     * Profile Settings Page
     *
     * @return void
     */
    public function profile_settings()
    {
        $this->set_user_reference( 'account' );
        $this->load->model( 'User_model' );
        
        $user = new stdClass;
        $user->two_factor_authentication = $this->zuser->get( 'two_factor_authentication' );
        $user->first_name = $this->zuser->get( 'first_name' );
        $user->last_name = $this->zuser->get( 'last_name' );
        $user->email_address = $this->zuser->get( 'email_address' );
        $user->picture = $this->zuser->get( 'picture' );
        $user->username = $this->zuser->get( 'username' );
        $user->about = $this->zuser->get( 'about' );
        $user->language = $this->zuser->get( 'language' );
        $user->country_id = $this->zuser->get( 'country_id' );
        $user->currency_id = $this->zuser->get( 'currency_id' );
        $user->gender = $this->zuser->get( 'gender' );
        $user->address_1 = $this->zuser->get( 'address_1' );
        $user->address_2 = $this->zuser->get( 'address_2' );
        $user->phone_number = $this->zuser->get( 'phone_number' );
        $user->company = $this->zuser->get( 'company' );
        $user->state = $this->zuser->get( 'state' );
        $user->city = $this->zuser->get( 'city' );
        $user->zip_code = $this->zuser->get( 'zip_code' );
        $user->time_format = $this->zuser->get( 'time_format' );
        $user->date_format = $this->zuser->get( 'date_format' );
        $user->timezone = $this->zuser->get( 'timezone' );
        $user->id = $this->zuser->get( 'id' );
        
        $data['data']['credits'] = $this->User_model->credits( $user->id );
        $data['data']['fields'] = $this->User_model->cf_data( $user->id );
        $data['title'] = lang( 'profile_settings' );
        $data['view'] = 'settings/profile_settings';
        $data['data']['user'] = $user;
        
        $this->load_panel_template( $data );
    }
    
    /**
     * Change Password Page
     *
     * @return void
     */
    public function change_password()
    {
        $this->set_user_reference( 'account' );
        
        $data['title'] = lang( 'change_password' );
        $data['view'] = 'settings/change_password';
        
        $this->load_panel_template( $data );
    }
    
    /**
     * Social Linkes Page
     *
     * @return  void
     * @version 1.4
     */
    public function social_links()
    {
        $this->set_user_reference( 'account' );
        
        $data['title'] = lang( 'social_links' );
        $data['view'] = 'settings/social_links';
        
        $this->load_panel_template( $data );
    }
}