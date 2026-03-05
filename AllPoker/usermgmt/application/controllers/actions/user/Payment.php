<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Payment Controller ( User, Actions )
 *
 * @author Shahzaib
 */
class Payment extends MY_Controller {
    
    /**
     * Logged-in User Premium Time
     *
     * @var integer
     * @version 1.3
     */
    private $user_premium_time;
    
    
    /**
     * Logged-in User ID
     *
     * @var integer
     * @version 1.3
     */
    private $user_id;
    
    
    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model( 'Payment_model' );
        $this->load->model( 'User_model' );
    }
    
    /**
     * Manage Purchase Type
     *
     * @param   object $item
     * @return  void
     * @version 1.3
     */
    private function manage_purchase_type( $item )
    {
        if ( $item->days > 0 )
        {
            $time_to_assign = $item->days * ( 24 * 60 * 60 );
            
            if ( $this->user_premium_time > time() )
            {
                if ( $this->zuser->get( 'premium_item_id' ) == $item->id )
                {
                    $time_to_assign += $this->user_premium_time;
                }
                else
                {
                    $time_to_assign += time();
                }
            }
            else
            {
                $time_to_assign += time();
            }
        }
        else
        {
            $time_to_assign = -1;
        }
        
        $this->User_model->assign_item( $this->user_id, $time_to_assign, $item->id );
    }
    
    /**
     * Throw Error
     *
     * @param   string $togo
     * @param   string @key
     * @param   boolean $move
     * @return  void
     * @version 1.3
     */
    private function throw_error( $togo, $key = '', $move = false )
    {
        if ( $this->input->is_ajax_request() )
        {
            if ( $move === true ) r_s_jump( $togo );
            else r_error( $key );
        }
        else if ( ! empty( $key ) )
        {
            error_redirect( $key, $togo );
        }
        else env_redirect( $togo );
    }
    
    /**
     * Proceed Payment Input Handling.
     *
     * @param  string $type
     * @return void
     */
    public function proceed( $type = 'stripe' )
    {
        if ( ! $this->zuser->is_logged_in ) $this->throw_error( 'login', '', true );
        
        $this->user_premium_time = $this->zuser->get( 'premium_time' );
        $this->user_id = $this->zuser->get( 'id' );
        $requester = 'user/payment/items';
        $id = intval( post( 'id' ) );
        $user = $this->User_model->get_by_id( $this->user_id );
        $item = $this->Payment_model->item( $id );
        
        if ( empty( $item ) ) $this->throw_error( $requester, 'went_wrong' );
        
        $currency = get_currency_code( $item->currency_id );
        
        // Verify the price in case the item price is changed while the payment is processing:
        if ( post( 'price' ) !== ( $item->price + 0 ) . ' ' . $currency )
        {
            $this->throw_error( $requester, 'went_wrong' );
        }
        
        if ( $item->status == 0 )
        {
            $this->throw_error( $requester, 'item_not_for_sale' );
        }
        
        if ( $item->type === 'purchase' && $this->user_premium_time == -1 )
        {
            $this->throw_error( $requester, 'unlimited_time' );
        }
        
        if ( $type === 'stripe' )
        {
            $token = do_secure( post( 'stripe_token' ) );
            
            if ( ! is_stripe_togo() ) error_redirect( 'missing_keys', $requester );
            
            if ( ! post( 'stripe_token' ) || ! $id ) error_redirect( 'invalid_req', $requester );
            
            $this->load->library( 'ZStripe', ['secret_api_key' => db_config( 'sp_secret_key' )] );
            
            $added = $this->zstripe->add_customer( $user->email_address, $token );
            
            if ( $added )
            {
                $charged = $this->zstripe->create_charge( $added->id, $item->name . ' Item', $item->price, strtolower( $currency ) );
                
                if ( $charged )
                {
                    if ( $charged['amount_refunded'] == 0 &&
                         empty( $charged['failure_code'] &&
                         $charged['paid'] == 1 &&
                         $charged['captured'] == 1 ) )
                    {
                        if ( $item->type === 'top_up' )
                        {
                            $this->User_model->add_credit( $this->user_id, $item->currency_id, $item->price );
                        }
                        else
                        {
                            $this->manage_purchase_type( $item );
                        }
                        
                        $this->Payment_model->update_item_sales( $item->id );
                        $this->Payment_model->log_stripe_payment_gateway( $this->user_id, $item, $charged );
                        
                        log_user_activity( 'paid_for_item', $item->name );
                        success_redirect( 'payed', $requester );
                    }
                    else
                    {
                        error_redirect( 'payment_failed', $requester );
                    }
                }
                else
                {
                    env_redirect( $requester );
                }
            }
            else
            {
                env_redirect( $requester );
            }
        }
        else if ( $type === 'credit' )
        {
            if ( db_config( 'credit_pay_enable' ) && $item->type !== 'top_up' )
            {
                $row = $this->User_model->credit( $this->user_id, $item->currency_id );
                
                if ( ! empty( $row ) )
                {
                    if ( doubleval( $row->credit ) < $item->price )
                    {
                        r_error( 'insufficient_credit' );
                    }
                    
                    $status = $this->User_model->cut_credit( $this->user_id, $item->currency_id, $item->price );
                    
                    if ( $status )
                    {
                        $this->manage_purchase_type( $item );
                        $this->Payment_model->update_item_sales( $item->id );
                        
                        $log = [
                            'user_id' => $this->user_id,
                            'item_id' => $item->id,
                            'currency_id' => $item->currency_id,
                            'item_name' => $item->name,
                            'gateway' => 'credit',
                            'amount' => $item->price
                        ];
                        
                        $this->Payment_model->log_pay_with_credit( $log );
                        log_user_activity( 'paid_for_item', $item->name );
                        
                        r_s_jump( $requester, 'payed' );
                    }
                    
                    r_error( 'went_wrong' );
                }
                
                r_error( 'dont_have_currency' );
            }
            
            r_error( 'invalid_req' );
        }
    }
}
