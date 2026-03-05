<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * User Model
 *
 * @author Shahzaib
 */
class User_model extends MY_Model
{

    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->table = 'users';
    }

    /**
     * Get by API Key
     *
     * @param   string $key
     * @return  object
     * @version 1.6
     */
    public function get_by_api_key($key)
    {
        $data['where'] = ['restful_api_key' => $key];

        return $this->get_one($data);
    }

    /**
     * Update User Activity Details ( API Interface ).
     *
     * @param   string  $timezone
     * @param   integer $id
     * @param   string  $token
     * @return  boolean
     * @version 1.6
     */
    public function update_user_activity_details($timezone, $id, $token)
    {
        // For Online Time:
        $user = $this->get_by_id($id);

        if (empty($user)) return false;

        if ($user->online_time < (time() - 60 * 5) || $user->is_online == 0) {
            $this->update([
                'data' => [
                    'online_date' => get_site_date('', $timezone),
                    'online_time' => time(),
                    'is_online' => 1
                ],
                'update_time' => false,
                'where' => ['id' => $id]
            ]);
        }

        // For Last Activity:
        $this->update([
            'where' => ['token' => $token],
            'data' => ['last_activity' => time()],
            'table' => 'users_sessions'
        ]);

        $this->update([
            'where' => ['id' => $id],
            'data' => ['last_activity' => time()],
            'update_time' => false,
            'table' => 'users'
        ]);

        return true;
    }

    /**
     * Get Unique Username
     *
     * @param  string $source
     * @return string
     */
    public function get_unique_username($source)
    {
        $username = str_replace(' ', '', $source);
        $result = 0;

        $data['like_column'] = 'username';
        $data['like_column_value'] = $username;

        $result = $this->get($data);

        if (! empty($result)) {
            $count = count($result);

            if ($count > 0) {
                $username .= $count;
            }
        }

        return strtolower($username);
    }

    /**
     * Get by Email Address
     *
     * @param  string $email_address
     * @return object
     */
    public function get_by_email($email_address)
    {
        $data['where'] = ['email_address' => $email_address];

        return $this->get_one($data);
    }

    /**
     * Get by Username
     *
     * @param  string $username
     * @return object
     */
    public function get_by_username($username)
    {
        $data['where'] = ['username' => $username];

        return $this->get_one($data);
    }

    /**
     * Get by ID
     *
     * @param  integer $user_id
     * @retrun object
     */
    public function get_by_id($user_id)
    {
        $data['column_value'] = $user_id;

        return $this->get_one($data);
    }
    public function get_by_id_cash($id)
    {
        // Join with user_credits to get cash_balance
        return $this->db
            ->select('u.id, u.username, uc.credit')
            ->from('users u')
            ->join('users_credits uc', 'uc.user_id = u.id', 'left')
            ->where('u.id', $id)
            ->get()
            ->row();
    }

    /**
     * Get Users Count by Month and Year
     *
     * @param  string $month_year
     * @return integer
     */
    public function get_count_by_month_year($month_year)
    {
        $data['where'] = ['registered_month_year' => $month_year];
        $data['table'] = 'users';

        return $this->get_count($data);
    }

    /**
     * Get Social Users Count
     *
     * @return integer
     */
    public function get_social_count()
    {
        $data['where'] = ['registration_source !=' => 1];
        $data['table'] = 'users';

        return $this->get_count($data);
    }

    /**
     * Get New Users Count
     *
     * Use to get the count of the users registered within 24 hrs.
     *
     * @return integer
     */
    public function get_of_new_count()
    {
        $data['where'] = ['registered_at >' => subtract_time('24 hours')];
        $data['table'] = 'users';

        return $this->get_count($data);
    }

    /**
     * Get Today's Online Users Count
     *
     * @return integer
     */
    public function get_online_today_count()
    {
        $data['where'] = ['online_date' => get_site_date()];
        $data['table'] = 'users';

        return $this->get_count($data);
    }

    /**
     * Get of Total Users Count
     *
     * @return integer
     */
    public function get_of_total_count()
    {
        return $this->get_count();
    }

    /**
     * Is Email Address Exists
     *
     * @param  string  $email_address
     * @param  integer $id
     * @return boolean
     */
    public function is_email_address_exists($email_address, $id)
    {
        $data['where'] = ['email_address' => $email_address, 'id !=' => $id];

        return ! empty($this->get_one($data));
    }

    /**
     * Is Username Exists
     *
     * @param  string  $username
     * @param  integer $id
     * @return boolean
     */
    public function is_username_exists($username, $id)
    {
        $data['where'] = ['username' => $username, 'id !=' => $id];

        return ! empty($this->get_one($data));
    }

    /**
     * Users
     *
     * @param  array $options
     * @return mixed
     */
    public function users(array $options = [])
    {
        $data = [];

        $data['select'] = 'users.*, roles.name as role_name';
        $data['join'] = ['table' => 'roles', 'on' => 'users.role = roles.id'];

        if (! empty($options['limit'])) $data['limit'] = $options['limit'];

        if (! empty($options['offset'])) $data['offset'] = $options['offset'];

        if (! empty($options['filter'])) {
            switch ($options['filter']) {
                case 'new_tfhrs':
                    $data['where'] = ['registered_at >' => subtract_time('24 hours')];
                    break;

                // @version 1.5:
                case 'online_today':
                    $data['where'] = ['online_date' => get_site_date()];
                    break;

                case 'premium_time':
                    $data['or_where'] = ['premium_time' => '-1', 'premium_time >' => time()];
                    break;

                case 'social':
                    $data['where'] = ['registration_source !=' => 1];
                    break;

                case 'online':
                    $data['where'] = ['is_online' => 1];
                    break;

                case 'offline':
                    $data['where'] = ['is_online' => 0];
                    break;

                case 'non_verified':
                    $data['where'] = ['is_verified' => 0];
                    break;

                case 'active':
                    $data['where'] = ['status' => 1];
                    break;

                case 'banned':
                    $data['where'] = ['status' => 0];
                    break;
            }
        }

        if (! empty($options['role'])) {
            $data['where']['role'] = $options['role'];
        }

        if (! empty($options['searched'])) {
            $holders = ['email_address', 'username', 'first_name', 'last_name'];

            foreach ($holders as $holder) {
                $data['like'][$holder] = $options['searched'];
            }
        }

        if (@$options['count'] === true) {
            return $this->get_count($data);
        }

        return $this->get($data);
    }

    /**
     * Limited Users
     *
     * @param  integer $limit
     * @return object
     */
    public function limited_users($limit)
    {
        return $this->users(['limit' => $limit]);
    }

    /**
     * Set Awayed Users as Offline
     *
     * @return void
     */
    public function set_awayed_offline()
    {
        $data['where'] = ['online_time <' => (time() - 60 * 15), 'is_online' => 1];
        $data['update_time'] = false;
        $data['data'] = ['is_online' => 0];

        $this->update($data);
    }

    /**
     * Update User
     *
     * @param  array   $to_update
     * @param  integer $id
     * @param  boolean $update_time
     * @return boolean
     */
    public function update_user($to_update, $id, $update_time = true)
    {
        $data['column_value'] = $id;
        $data['update_time'] = $update_time;
        $data['data'] = $to_update;

        return $this->update($data);
    }

    /**
     * Assign Item to User
     *
     * @param  integer $user_id
     * @param  integer $time
     * @param  integer $item_id
     * @return boolean
     */
    public function assign_item($user_id, $time, $item_id)
    {
        $to_update = ['premium_time' => $time, 'premium_item_id' => $item_id];

        return $this->update_user($to_update, $user_id, false);
    }

    /**
     * Sent Email Records ( User )
     *
     * @param   array $options
     * @return  mixed
     * @version 1.5
     */
    public function sent_emails(array $options = [])
    {
        $data['select'] = 'users_sent_emails.*, users.first_name, users.last_name';
        $data['table'] = 'users_sent_emails';
        $data['where'] = ['sent_to' => $options['user_id']];
        $data['join'] = ['table' => 'users', 'on' => 'users.id = users_sent_emails.sent_by'];

        if (! empty($options['limit'])) $data['limit'] = $options['limit'];

        if (! empty($options['offset'])) $data['offset'] = $options['offset'];

        if (@$options['count'] === true) {
            return $this->get_count($data);
        }

        return $this->get($data);
    }

    /**
     * Sent Email Record ( User )
     *
     * @param   integer $id
     * @return  object
     * @version 1.5
     */
    public function sent_email($id)
    {
        $data['where']['id'] = $id;
        $data['table'] = 'users_sent_emails';

        return $this->get_one($data);
    }

    /**
     * Add Sent Email Record ( User ).
     *
     * @param   array $data
     * @return  mixed
     * @version 1.5
     */
    public function add_sent_email($data)
    {
        return $this->add($data, 'users_sent_emails');
    }

    /**
     * Delete Sent Email Record ( User ).
     *
     * @param   integer $id
     * @return  boolean
     * @version 1.5
     */
    public function delete_sent_email($id)
    {
        $data['table'] = 'users_sent_emails';
        $data['column_value'] = $id;

        return $this->delete($data);
    }

    /**
     * Delete User
     *
     * @param  integer $id
     * @return boolean
     */
    public function delete_user($id)
    {
        $data['column_value'] = $id;

        return $this->delete($data);
    }

    /**
     * Custom Fields Data
     *
     * @param  integer $user_id
     * @return object
     */
    public function cf_data($user_id)
    {
        $data['select'] = 'cf.*, ucf.value';
        $data['table'] = 'custom_fields cf';

        $data['join'] = [
            'table' => 'users_custom_fields ucf',
            'on' => "ucf.custom_field_id = cf.id AND ucf.user_id = {$user_id}"
        ];

        $data['orderby_column'] = 'cf.id';
        $data['order'] = 'ASC';

        return $this->get($data);
    }

    /**
     * Custom Field Data Row
     *
     * @param  integer $user_id
     * @param  integer $custom_field_id
     * @return object
     */
    public function cf_row($user_id, $custom_field_id)
    {
        $data['where'] = ['user_id' => $user_id, 'custom_field_id' => $custom_field_id];
        $data['table'] = 'users_custom_fields';

        return $this->get_one($data);
    }

    /**
     * Manage Custom Field Data
     *
     * @param  array $options
     * @return void
     */
    public function manage_cf_data($options)
    {
        $data['table'] = 'users_custom_fields';

        if (! empty($this->cf_row($options['user_id'], $options['custom_field_id']))) {
            $data['where']['user_id'] = $options['user_id'];
            $data['where']['custom_field_id'] = $options['custom_field_id'];
            $data['data']['value'] = $options['value'];

            $this->update($data);
        } else {
            $this->add($options, $data['table']);
        }
    }

    /**
     * Countries
     *
     * @return object
     */
    public function countries()
    {
        $data['table'] = 'countries';
        $data['order'] = 'ASC';

        return $this->get($data);
    }

    /**
     * Add Credit
     *
     * @param  integer $user_id
     * @param  integer $currency_id
     * @param  integer $to_add
     * @return mixed
     */
    public function add_credit($user_id, $currency_id, $to_add)
    {
        $data['where'] = ['user_id' => $user_id, 'currency_id' => $currency_id];
        $data['table'] = 'users_credits';

        if (empty($this->get_one($data))) {
            unset($data['where']);

            $to_add = [
                'credit' => $to_add,
                'currency_id' => $currency_id,
                'user_id' => $user_id
            ];

            return $this->add($to_add, $data['table']);
        } else {
            $data['set'] = ['credit' => "credit+{$to_add}"];

            return $this->update($data);
        }
    }

    /**
     * Cut Credit
     *
     * @param  integer $user_id
     * @param  integer $currency_id
     * @param  integer $to_cut
     * @return boolean
     */
    public function cut_credit($user_id, $currency_id, $to_cut)
    {
        $data['where'] = ['user_id' => $user_id, 'currency_id' => $currency_id];
        $data['table'] = 'users_credits';
        $data['set'] = ['credit' => "credit-{$to_cut}"];

        return $this->update($data);
    }

    /**
     * Credits
     *
     * @param  integer $user_id
     * @return object
     */
    public function credits($user_id)
    {
        $data['select'] = 'uc.credit, c.code';
        $data['table'] = 'users_credits uc';
        $data['join'] = ['table' => 'currencies c', 'on' => 'c.id = uc.currency_id'];
        $data['orderby_column'] = 'uc.id';
        $data['where'] = ['user_id' => $user_id];

        return $this->get($data);
    }

    /**
     * Credit
     *
     * @param  integer $user_id
     * @param  integer $currency_id
     * @return object
     */
    public function credit($user_id, $currency_id)
    {
        $data['where'] = ['user_id' => $user_id, 'currency_id' => $currency_id];
        $data['table'] = 'users_credits';

        return $this->get_one($data);
    }

    /**
     * Delete User Credit
     *
     * @param  integer $id
     * @return void
     */
    public function delete_user_credit($id)
    {
        $data['where']['user_id'] = $id;
        $data['table'] = 'users_credits';

        $this->delete($data);
    }

    /**
     * Mark Email Address as Verified.
     *
     * @param  integer $user_id
     * @return boolean
     */
    public function mark_as_everified($user_id)
    {
        $data['data']['is_verified'] = 1;
        $data['column_value'] = $user_id;

        return $this->update($data);
    }

    /**
     * Update Password
     *
     * @param  integer $id
     * @param  string  $password
     * @return boolean
     */
    public function update_password($id, $password)
    {
        $data['column_value'] = $id;
        $password = password_hash($password, PASSWORD_DEFAULT);
        $data['data'] = ['password' => $password];

        return $this->update($data);
    }

    /**
     * Invites
     *
     * @param  array $options
     * @return mixed
     */
    public function invites(array $options = [])
    {
        $data['select'] = 'ui.*, u.first_name, u.last_name';
        $data['join'] = ['table' => 'users u', 'on' => 'u.id = ui.user_id'];
        $data['table'] = 'users_invites ui';

        if (! empty($options['id'])) {
            $data['where'] = ['ui.id' => $options['id']];
            return $this->get_one($data);
        }

        if (! empty($options['limit'])) $data['limit'] = $options['limit'];

        if (! empty($options['offset'])) $data['offset'] = $options['offset'];

        if (@$options['count'] === true) {
            return $this->get_count($data);
        }

        return $this->get($data);
    }

    /**
     * Invitation by ID
     *
     * @param  string $code
     * @return object
     */
    public function invitation_by_code($code)
    {
        $data['where'] = ['invitation_code' => $code];
        $data['table'] = 'users_invites';

        return $this->get_one($data);
    }

    /**
     * Add Invitation
     *
     * @param  array $data
     * @return mixed
     */
    public function add_invitation($data)
    {
        return $this->add($data, 'users_invites');
    }

    /**
     * Update Invitation
     *
     * @param  array   $to_update
     * @param  integer $id
     * @return boolean
     */
    public function update_invitation($to_update, $id)
    {
        $data['column_value'] = $id;
        $data['table'] = 'users_invites';
        $data['data'] = $to_update;

        return $this->update($data);
    }

    /**
     * Mark as Used the Invitation.
     *
     * @param  string  $code
     * @param  integer $user_id
     * @return boolean
     */
    public function invitation_mark_as_used($code, $user_id)
    {
        $data['column'] = 'invitation_code';
        $data['column_value'] = $code;
        $data['table'] = 'users_invites';
        $data['update_time'] = false;
        $data['data'] = ['status' => 1, 'user_id' => $user_id];

        return $this->update($data);
    }

    /**
     * Delete Invitation
     *
     * @param  integer $id
     * @return boolean
     */
    public function delete_invitation($id)
    {
        $data['column_value'] = $id;
        $data['table'] = 'users_invites';

        return $this->delete($data);
    }
    public function add_cash_balance($user_id, $amount)
    {
        // Check if the record exists for this user in users_credits
        $exists = $this->db->where('user_id', $user_id)->get('users_credits')->row();

        if ($exists) {
            // Increment credit
            $this->db->set('credit', 'credit + ' . floatval($amount), false);
            $this->db->where('user_id', $user_id);
            return $this->db->update('users_credits');
        } else {
            // Create a new record if none exists
            $data = [
                'user_id' => $user_id,
                'credit' => floatval($amount)
            ];
            return $this->db->insert('users_credits', $data);
        }
    }
    public function get_cash_by_user_id($user_id)
    {
        return $this->db
            ->select('users.id, users.username, users_credits.user_id, users_credits.credit')
            ->from('users_credits')
            ->join('users', 'users.id = users_credits.user_id', 'left')
            ->where('users_credits.user_id', $user_id)
            ->get()
            ->row();
    }

    public function cut_cash_balance($user_id, $amount)
    {
        // Deduct credit (make sure not to go below zero)
        $this->db->set('credit', 'credit - ' . floatval($amount), false);
        $this->db->where('user_id', $user_id);
        $this->db->where('credit >=', floatval($amount)); // prevent negative balances
        return $this->db->update('users_credits');
    }
}
