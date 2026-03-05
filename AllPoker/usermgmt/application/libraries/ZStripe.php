<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

require_once APPPATH . 'third_party/stripe/init.php';

/**
 * ZStripe Library
 *
 * Helpful to integrate the gateway. It is dependent on Stripe PHP bindings
 * and also requires Stripe API configuration details.
 *
 * @author Shahzaib
 */
class ZStripe {
    
    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct( $key )
    {
        \Stripe\Stripe::setApiKey( $key['secret_api_key'] );
    }
    
    /**
     * Add Customer
     *
     * @param  string $email_address
     * @param  string $token
     * @return mixed
     */
    public function add_customer( $email_address, $token )
    {
        try
        {
            return \Stripe\Customer::create([
                'email' => $email_address,
                'source' => $token
            ]);
        }
        catch ( Exception $e )
        {
            set_error_flash( $e->getMessage(), 'direct' );
            return false;
        }
    }
    
    /**
     * Create Charge
     *
     * @param  integer $customer_id
     * @param  string  $item
     * @param  integer $price
     * @param  string  $currency
     * @return mixed
     */
    public function create_charge( $customer_id, $item, $price, $currency )
    {
        $amount = ( $price * 100 );
        
        if ( in_array( $currency, ZERO_DECIMAL_ISO ) ) $amount = intval( $price );
        
        try
        {
            $charge = \Stripe\Charge::create([
                'customer' => $customer_id,
                'amount' => $amount,
                'currency' => $currency,
                'description' => $item
            ]);
            
            return $charge->jsonSerialize();
        }
        catch ( Exception $e )
        {
            set_error_flash( $e->getMessage(), 'direct' );
            return false;
        }
    }
}
