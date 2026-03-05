<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Payment Model
 *
 * @author Shahzaib
 */
class Payment_model extends MY_Model {
    
    /**
     * Currencies
     *
     * @return object
     */
    public function currencies()
    {
        $data['table'] = 'currencies';
        $data['order'] = 'ASC';
        
        return $this->get( $data );
    }
    
    /**
     * Currency
     *
     * @param  integer $id
     * @return object
     */
    public function currency( $id )
    {
        $data['column_value'] = $id;
        $data['table'] = 'currencies';
        
        return $this->get_one( $data );
    }
    
    /**
     * Payments Log
     *
     * @param  array $optionns
     * @return mixed
     */
    public function payments_log( array $options = [] )
    {
        $data['select'] = 'payments_log.*, c.code, u.email_address';
        $data['table'] = 'payments_log';
        
        $data['join'] = [
            ['table' => 'currencies c', 'on' => 'c.id = payments_log.currency_id'],
            ['table' => 'users u', 'on' => 'u.id = payments_log.user_id'],
        ];
        
        if ( ! empty( $options['user_id'] ) )
        {
            $data['where'] = ['user_id' => $options['user_id'], 'visible_to_user' => 1];
        }
        
        if ( ! empty( $options['hash'] ) || ! empty( $options['id'] ) )
        {
            if ( ! empty( $options['hash'] ) )
            {
                $data['where']['hash'] = $options['hash'];
            }
            else
            {
                $data['where']['payments_log.id'] = $options['id'];
            }
            
            return $this->get_one( $data );
        }
        
        if ( ! empty( $options['limit'] ) ) $data['limit'] = $options['limit'];
        
        if ( ! empty( $options['offset'] ) ) $data['offset'] = $options['offset'];
        
        if ( @$options['count'] === true )
        {
            return $this->get_count( $data );
        }
        
        return $this->get( $data );
    }
    
    /**
     * Payment Log
     *
     * @param  integer $user_id
     * @param  string  $hash
     * @return object
     */
    public function payment_log( $user_id, $hash )
    {
        return $this->payments_log( ['user_id' => $user_id, 'hash' => $hash] );
    }
    
    /**
     * Log Payment ( Stripe Gateway ).
     *
     * @param  integer $user_id
     * @param  object  $item
     * @param  array   $charged
     * @return void
     */
    public function log_stripe_payment_gateway( $user_id, $item, $charged )
    {
        $transaction_id = $charged['balance_transaction'];
        
        $data = [
            'user_id' => $user_id,
            'currency_id' => $item->currency_id,
            'item_id' => $item->id,
            'item_name' => $item->name,
            'gateway' => 'stripe',
            'hash' => get_short_random_string( $transaction_id ),
            'transaction_id' => $transaction_id,
            'amount' => $item->price,
            'status' => $charged['status'],
            'performed_at' => time()
        ];
        
        $this->add( $data, 'payments_log' );
    }
    
    /**
     * Log Payment ( Adjust Balance ).
     *
     * @param  array $options
     * @return void
     */
    public function log_adjust_balance( $options )
    {
        $data = [
            'user_id' => $options['user_id'],
            'currency_id' => $options['currency_id'],
            'item_name' => $options['item_name'],
            'gateway' => $options['gateway'],
            'hash' => get_short_random_string(),
            'amount' => $options['amount'],
            'status' => 'succeeded',
            'performed_at' => time()
        ];
        
        if ( isset( $options['visible_to_user'] ) )
        {
            $data['visible_to_user'] = $options['visible_to_user'];
        }
        
        if ( isset( $options['create_invoice'] ) )
        {
            $data['create_invoice'] = $options['create_invoice'];
        }
        
        if ( isset( $options['item_id'] ) )
        {
            $data['item_id'] = $options['item_id'];
        }
        
        $this->add( $data, 'payments_log' );
    }
    
    /**
     * Log Payment ( Through Credit ).
     *
     * @param   array $data
     * @return  void
     * @version 1.3
     */
    public function log_pay_with_credit( $data )
    {
        $this->Payment_model->log_adjust_balance( $data );
    }
    
    /**
     * Delete Payment Log
     *
     * @param  integer $id
     * @return boolean
     */
    public function delete_log( $id )
    {
        $data['column_value'] = $id;
        $data['table'] = 'payments_log';

        return $this->delete( $data );
    }
    
    /**
     * Items
     *
     * @param  integer $status
     * @return object
     */
    public function items( $status = null )
    {
        $data['select'] = 'payment_items.*, currencies.code';
        $data['table'] = 'payment_items';
        $data['join'] = ['table' => 'currencies', 'on' => 'currencies.id = payment_items.currency_id'];
        
        if ( $status !== null )
        {
            $data['where'] = ['status' => $status];
        }
        
        $data['order'] = 'ASC';
        
        return $this->get( $data );
    }
    
    /**
     * Item
     *
     * @param  integer $id
     * @return object
     */
    public function item( $id )
    {
        $data['column_value'] = $id;
        $data['table'] = 'payment_items';
        
        return $this->get_one( $data );
    }
    
    /**
     * Add Item
     *
     * @param  array $data
     * @return mixed
     */
    public function add_item( $data )
    {
        return $this->add( $data, 'payment_items' );
    }
    
    /**
     * Update Item
     *
     * @param  array   $to_update
     * @param  integer $id
     * @return boolean
     */
    public function update_item( $to_update, $id )
    {
        $data['column_value'] = $id;
        $data['table'] = 'payment_items';
        $data['data'] = $to_update;

        return $this->update( $data );
    }
    
    /**
     * Update Item Sales
     *
     * @param  integer $id
     * @return boolean
     */
    public function update_item_sales( $id )
    {
        $data['column_value'] = $id;
        $data['update_time'] = false;
        $data['table'] = 'payment_items';
        $data['set'] = ['sales' => 'sales+1'];
        
        return $this->update( $data );
    }
    
    /**
     * Delete Item
     *
     * @param  integer $id
     * @return boolean
     */
    public function delete_item( $id )
    {
        $data['column_value'] = $id;
        $data['table'] = 'payment_items';

        return $this->delete( $data );
    }
}
