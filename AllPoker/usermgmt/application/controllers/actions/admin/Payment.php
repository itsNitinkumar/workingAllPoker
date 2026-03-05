<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Payment Controller ( Admin, Actions )
 *
 * @author Shahzaib
 */
class Payment extends MY_Controller {
    
    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        if ( ! $this->zuser->is_logged_in ) r_s_jump( 'login' );
        
        if ( ! $this->zuser->has_permission( 'payment' ) )
        {
            r_error_no_permission();
        }
        
        $this->load->library( 'form_validation' );
        $this->load->model( 'Payment_model' );
    }
    
    /**
     * Add Item Input Handling.
     *
     * @return void
     */
    public function add_item()
    {
        if ( $this->form_validation->run( 'payment_item' ) )
        {
            $data = [
                'name' => do_secure( post( 'name' ) ),
                'type' => do_secure_l( post( 'type' ) ),
                'currency_id' => intval( post( 'currency' ) ),
                'price' => do_secure( post( 'price' ) ),
                'days' => intval( post( 'days' ) ),
                'description' => do_secure( post( 'description' ) ),
                'status' => only_binary( post( 'status' ) ),
                'created_at' => time()
            ];
            
            if ( empty( get_currency_by_id( $data['currency_id'] ) ) )
            {
                r_error( 'invalid_req' );
            }
            
            $id = $this->Payment_model->add_item( $data );
            
            if ( ! empty( $id ) )
            {
                $data['id'] = $id;
                
                $html = read_view( 'admin/responses/add_payment_item', $data );
                
                log_user_activity( 'payment_item_added', $id );
                r_success_add( $html );
            }
            
            r_error( 'went_wrong' );
        }
        
        d_r_error( validation_errors() );
    }
    
    /**
     * Edit Item ( Response )
     *
     * @return void
     */
    public function edit_item()
    {
        if ( ! post( 'id' ) ) r_error( 'invalid_req' );
        
        $data = $this->Payment_model->item( intval( post( 'id' ) ) );
        
        if ( ! empty( $data ) )
        {
            display_view( 'admin/responses/forms/edit_payment_item', $data );
        }
        
        r_error( 'invalid_req' );
    }
    
    /**
     * Update Item Input Handling.
     *
     * @return void
     */
    public function update_item()
    {
        if ( $this->form_validation->run( 'payment_item' ) )
        {
            $id = intval( post( 'id' ) );
            
            $data = [
                'name' => do_secure( post( 'name' ) ),
                'type' => do_secure_l( post( 'type' ) ),
                'currency_id' => intval( post( 'currency' ) ),
                'price' => do_secure( post( 'price' ) ),
                'days' => intval( post( 'days' ) ),
                'description' => do_secure( post( 'description' ) ),
                'status' => only_binary( post( 'status' ) )
            ];
            
            if ( empty( get_currency_by_id( $data['currency_id'] ) ) )
            {
                r_error( 'invalid_req' );
            }
            
            if ( $this->Payment_model->update_item( $data, $id ) )
            {
                $data = $this->Payment_model->item( $id );
                $html = read_view( 'admin/responses/update_payment_item', $data );
                
                log_user_activity( 'payment_item_updated', $id );
                r_success_replace( $id, $html );
            }
            
            r_error( 'not_updated' );
        }
        
        d_r_error( validation_errors() );
    }
    
    /**
     * Delete Item
     *
     * @return void
     */
    public function delete_item()
    {
        $id = intval( post( 'id' ) );
        
        if ( $this->Payment_model->delete_item( $id ) )
        {
            log_user_activity( 'payment_item_deleted', $id );
            r_success_remove( $id );
        }
        
        r_error( 'invalid_req' );
    }
    
    /**
     * Payment Log Details
     *
     * @return void
     */
    public function log_details()
    {
        if ( ! post( 'id' ) ) r_error( 'invalid_req' );
        
        $data = $this->Payment_model->payments_log( ['id' =>intval( post( 'id' ) )] );
        
        if ( ! empty( $data ) )
        {
            display_view( 'admin/responses/payment_log_details', $data );
        }
        
        r_error( 'invalid_req' );
    }
    
    /**
     * Delete Payment Log
     *
     * @return void
     */
    public function delete_log()
    {
        $id = intval( post( 'id' ) );
        
        if ( $this->Payment_model->delete_log( $id ) )
        {
            log_user_activity( 'payment_log_deleted', $id );
            r_success_remove( $id );
        }
        
        r_error( 'invalid_req' );
    }
}
