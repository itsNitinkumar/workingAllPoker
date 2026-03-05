<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Support Model
 *
 * @author Shahzaib
 */
class Support_model extends MY_Model {
    
    /**
     * Contact Messages
     *
     * @param  string  $type
     * @param  boolean $count
     * @param  integer $limit
     * @param  integer $offset
     * @return mixed
     */
    public function contact_messages( $type, $count = false, $limit = 0, $offset = 0 )
    {
        if ( $type === 'replied' )
        {
            $data['where'] = ['replied_at !=' => null];
        }
        else if ( $type === 'not_replied' )
        {
            $data['where'] = ['replied_at' => null];
        }
        
        $data['limit'] = $limit;
        $data['offset'] = $offset;
        $data['table'] = 'contact_messages';
        
        if ( $count === true )
        {
            return $this->get_count( $data );
        }
        
        return $this->get( $data );
    }
    
    /**
     * Contact Message
     *
     * @param  integer $id
     * @return object
     */
    public function contact_message( $id )
    {
        $data['column_value'] = $id;
        $data['table'] = 'contact_messages';
        
        return $this->get_one( $data );
    }
    
    /**
     * Add Contact Message
     *
     * @param  array $data
     * @return mixed
     */
    public function add_contact_message( $data )
    {
        return $this->add( $data, 'contact_messages' );
    }
    
    /**
     * Add Contact Message Reply
     *
     * @param  integer $id
     * @param  string  $text
     * @return boolean
     */
    public function add_contact_message_reply( $id, $text )
    {
        $to_update = ['replied_at' => time(), 'reply' => $text];
        
        $data['column_value'] = $id;
        $data['table'] = 'contact_messages';
        $data['data'] = $to_update;

        return $this->update( $data );
    }
    
    /**
     * Not Read Contact Messages ( Count ).
     *
     * @return integer
     */
    public function not_read_cm()
    {
        $data['where'] = ['is_read' => 0];
        $data['table'] = 'contact_messages';
        
        return $this->get_count( $data );
    }
    
    /**
     * Mark as Read the Contact Message.
     *
     * @param  integer $id
     * @return void
     */
    public function cm_mark_as_read( $id )
    {
        $data['column_value'] = $id;
        $data['table'] = 'contact_messages';
        $data['data'] = ['is_read' => 1];
       
        $this->update( $data );
    }
    
    /**
     * Delete Contact Message
     *
     * @param  integer $id
     * @return boolean
     */
    public function delete_contact_message( $id )
    {
        $data['column_value'] = $id;
        $data['table'] = 'contact_messages';
        
        return $this->delete( $data );
    }
    
    /**
     * Categories
     *
     * @return object
     */
    public function categories()
    {
        $data['table'] = 'tickets_categories';
        
        return $this->get( $data );
    }
    
    /**
     * Category
     *
     * @param  integer $id
     * @return object
     */
    public function category( $id )
    {
        $data['column_value'] = $id;
        $data['table'] = 'tickets_categories';
        
        return $this->get_one( $data );
    }
    
    /**
     * Add Category
     *
     * @param  array $data
     * @return mixed
     */
    public function add_category( $data )
    {
        return $this->add( $data, 'tickets_categories' );
    }
    
    /**
     * Update Category
     *
     * @param  array   $to_update
     * @param  integer $id
     * @return boolean
     */
    public function update_category( $to_update, $id )
    {
        $data['column_value'] = $id;
        $data['table'] = 'tickets_categories';
        $data['data'] = $to_update;
       
        return $this->update( $data );
    }
    
    /**
     * Delete Category
     *
     * @param  integer $id
     * @return boolean
     */
    public function delete_category( $id )
    {
        $data['column_value'] = $id;
        $data['table'] = 'tickets_categories';
        
        return $this->delete( $data );
    }
    
    /**
     * Tickets
     *
     * @param  integer $user_id
     * @param  boolean $count
     * @param  integer $limit
     * @param  integer $offset
     * @return mixed
     */
    public function tickets( $user_id = 0, $count = false, $limit = 0, $offset = 0 )
    {
        $data['table'] = 'tickets';
        $data['order'] = 'ASC';
        
        if ( ! empty( $user_id ) )
        {
            $data['where'] = ['user_id' => $user_id];
        }
        
        $data['limit'] = $limit;
        $data['offset'] = $offset;
        
        if ( $count === true )
        {
            return $this->get_count( $data );
        }
        
        return $this->get( $data );
    }
    
    /**
     * Pending Ticket User ( Count ).
     *
     * @param  integer $user_id
     * @return integer
     */
    public function pending_ticket_user( $user_id )
    {
        $data['where'] = ['is_read' => 0, 'last_message_area' => 'admin', 'user_id' => $user_id];
        $data['table'] = 'tickets';
        
        return $this->get_count( $data );
    }
    
    /**
     * Pending Ticket Admin ( Count ).
     *
     * @return integer
     */
    public function pending_ticket_admin()
    {
        $data['where'] = ['is_read' => 0, 'last_message_area' => 'user'];
        $data['table'] = 'tickets';
        
        return $this->get_count( $data );
    }
    
    /**
     * Ticket Replies
     *
     * @param  integer $ticket_id
     * @return object
     */
    public function tickets_replies( $ticket_id )
    {
        $select = 'tr.*, u.id as user_id, u.first_name, u.last_name,';
        $select .= 'u.picture as user_picture';
        
        $data['select'] = $select;
        $data['table'] = 'tickets_replies tr';
        $data['join'] = ['table' => 'users u', 'on' => 'u.id = tr.user_id'];
        $data['where'] = ['tr.ticket_id' => $ticket_id];
        $data['order'] = 'ASC';
        
        return $this->get( $data );
    }
    
    /**
     * Ticket
     *
     * @param  integer $id
     * @param  integer $user_id
     * @return object
     */
    public function ticket( $id, $user_id = 0 )
    {
        $select = 't.id, t.subject, t.message, t.priority, t.attachment,';
        $select .= 't.attachment_name, t.status, t.updated_at, t.created_at,';
        $select .= 'tc.name as category, u.id as user_id, u.first_name,';
        $select .= 'u.last_name, u.picture as user_picture, t.last_message_area,';
        $select .= 't.is_read';
        
        $data['select'] = $select;
        $data['table'] = 'tickets t';
        $data['where'] = ['t.id' => $id];
        
        $data['join'] = [
            ['table' => 'tickets_categories tc', 'on' => 'tc.id = t.category_id'],
            ['table' => 'users u', 'on' => 'u.id = t.user_id']
        ];
        
        if ( ! empty( $user_id ) )
        {
            $data['where']['t.user_id'] = $user_id;
        }
        
        return $this->get_one( $data );
    }
    
    /**
     * Add Ticket
     *
     * @param  array $data
     * @return mixed
     */
    public function add_ticket( $data )
    {
        return $this->add( $data, 'tickets' );
    }
    
    /**
     * Update Ticket
     *
     * @param  array   $to_update
     * @param  integer $id
     * @param  boolean $update_time
     * @return boolean
     */
    public function update_ticket( $to_update, $id, $update_time = true )
    {
       $data['column_value'] = $id;
       $data['table'] = 'tickets';
       $data['update_time'] = false;
       $data['data'] = $to_update;
       
       if ( $update_time === true )
       {
            $data['data']['updated_at'] = time();
       }
       
       return $this->update( $data );
    }
    
    /**
     * Add Reply
     *
     * @param  array $data
     * @return mixed
     */
    public function add_reply( $data )
    {
        return $this->add( $data, 'tickets_replies' );
    }
    
    /**
     * Update Reply
     *
     * @param   array   $to_update
     * @param   integer $id
     * @return  boolean
     * @version 2.0
     */
    public function update_reply( $to_update, $id )
    {
       $data['column_value'] = $id;
       $data['table'] = 'tickets_replies';
       $data['data'] = $to_update;
       
       return $this->update( $data );
    }
    
    /**
     * Update Ticket Status
     *
     * @param  integer $id
     * @param  integer $status
     * @return boolean
     */
    private function update_ticket_status( $id, $status )
    {
        $data['column_value'] = $id;
        $data['table'] = 'tickets';
        $data['data'] = ['status' => $status];

        return $this->update( $data );
    }
    
    /**
     * Re-open Ticket
     *
     * @param  integer $id
     * @return boolean
     */
    public function reopen_ticket( $id )
    {
        return $this->update_ticket_status( $id, 1 );
    }
    
    /**
     * Ticket Reply
     *
     * @param  integer $id
     * @return object
     */
    public function ticket_reply( $id )
    {
        $data['column_value'] = $id;
        $data['table'] = 'tickets_replies';
        
        return $this->get_one( $data );
    }
    
    /**
     * Close Ticket
     *
     * @param  integer $id
     * @return boolean
     */
    public function close_ticket( $id )
    {
        return $this->update_ticket_status( $id, 0 );
    }
    
    /**
     * Delete Ticket Reply
     *
     * @param  integer $id
     * @return boolean
     */
    public function delete_ticket_reply( $id )
    {
        $data['column_value'] = $id;
        $data['table'] = 'tickets_replies';
        
        return $this->delete( $data );
    }
    
    /**
     * Delete Ticket Replies
     *
     * @parma  integer $id
     * @return void
     */
    public function delete_ticket_replies( $id )
    {
        $data['where']['ticket_id'] = $id;
        $data['table'] = 'tickets_replies';
        
        $this->delete( $data );
    }
    
    /**
     * Delete Ticket
     *
     * @param  integer $id
     * @return boolean
     */
    public function delete_ticket( $id )
    {
        $data['column_value'] = $id;
        $data['table'] = 'tickets';
        
        return $this->delete( $data );
    }
}
