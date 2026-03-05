<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'third_party/restserver/RestController.php';
require_once APPPATH . 'third_party/restserver/Format.php';

use chriskacerguis\RestServer\RestController;

/**
 * Api Controller
 *
 * Use to reach to the Users module ( for login etc ) from your other application(s).
 * The API key is required to reach the methods. It is because to restrict the
 * API access only for private use.
 *
 * @author  Shahzaib
 * @version 1.6
 */
class Api extends RestController
{

    /**
     * API Holder User
     *
     * @var object
     */
    private $api_holder;

    /**
     * Database Configuration
     *
     * @var object
     */
    private $db_config;


    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('form_validation');
        $this->load->model('User_model');
        $this->load->model('Login_model');
        $this->load->model('Setting_model');

        $api_key = $this->input->get_request_header('X-API-KEY');
        $this->api_holder = $this->User_model->get_by_api_key($api_key);
        $this->db_config = get_settings();

        // Check the API key holder account status, if banned,
        // restrict the access of API:
        if (! empty($this->api_holder) && @$this->api_holder->status == 0) {
            $this->response([
                'message' => $this->error_message('api_holder'),
                'status' => false
            ], self::HTTP_FORBIDDEN);
        }

        // Check for the maintenance mode, if enabled, then only
        // accessible for the allowed IP addresses:
        if ($this->db_config['maintenance_mode']) {
            $ip_address = $this->input->ip_address();
            $addresses = get_mm_allowed_ips($this->db_config['mm_allowed_ips']);

            if (! in_array($ip_address, $addresses)) {
                $this->response([
                    'message' => html_escape($this->db_config['mm_message']),
                    'status' => false
                ], self::HTTP_NOT_ACCEPTABLE);
            }
        }

        // Check the module status of the RESTful API module:
        if ($this->db_config['enable_restful_api'] == 0) {
            $this->response([
                'message' => $this->error_message('api_module_disabled'),
                'status' => false
            ], self::HTTP_NOT_ACCEPTABLE);
        }
    }

    /**
     * Is Authorized
     *
     * Use to prevent access if the API keyholder is not a default user.
     *
     * @return void
     */
    private function is_authorized()
    {
        $user_id = $this->api_holder->id;

        if ($user_id != 1)
            $this->response([
                'message' => $this->error_message('no_permission'),
                'status' => false
            ], self::HTTP_FORBIDDEN);
    }

    /**
     * User Login Authentication
     *
     * Use to authenticate the user and get the data
     * after the successful authentication.
     *
     * @api_header string X-API-KEY
     * @api_param  string username
     * @api_param  string password
     * @return     object JSON
     */
    public function login_post()
    {
        $this->is_authorized();

        $user_token = get_long_random_string();

        if ($this->form_validation->run('login')) {
            $username = do_secure_l(post('username'));

            // Check the account status through the user login attempts:
            if (db_config('u_temporary_lockout') !== 'off') {
                $status = user_locally_locked_check($username, 'login', true);

                if (! empty($status)) {
                    $this->response([
                        'message' => $status,
                        'status' => false
                    ], self::HTTP_NOT_ACCEPTABLE);
                }
            }

            if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
                $user = $this->User_model->get_by_email($username);
            } else {
                $user = $this->User_model->get_by_username($username);
            }

            if (! empty($user)) {
                if (! password_verify(post('password'), $user->password)) {
                    $this->Login_model->log_invalid_attempt($username);

                    $this->response([
                        'message' => $this->error_message('invalid_credentials'),
                        'status' => false
                    ], self::HTTP_NOT_ACCEPTABLE);
                } else {
                    $this->Login_model->delete_invalid_attempt($username);

                    if ($user->status == 0) {
                        $this->response([
                            'message' => $this->error_message('user_banned'),
                            'status' => false
                        ], self::HTTP_NOT_ACCEPTABLE);
                    }

                    if (set_login($user_token, $user->id, false, true)) {
                        $data = $user;

                        log_user_activity('user_logged_in_api', '', $user->id);
                        unset($data->password, $data->restful_api_key);

                        $data->token = $user_token;

                        $this->response([
                            'data' => $data,
                            'message' => $this->success_message('user_logged_in_api'),
                            'status' => true
                        ], self::HTTP_OK);
                    }

                    $this->response([
                        'message' => $this->error_message('went_wrong'),
                        'status' => false
                    ], self::HTTP_NOT_ACCEPTABLE);
                }
            } else {
                $this->Login_model->log_invalid_attempt($username);

                $this->response([
                    'message' => $this->error_message('invalid_credentials'),
                    'status' => false
                ], self::HTTP_NOT_ACCEPTABLE);
            }
        }

        $this->response([
            'message' => validation_errors(),
            'status' => false
        ], self::HTTP_NOT_ACCEPTABLE);
    }

    /**
     * Validate User Login Session
     *
     * Use to validate the logged in user and update the related
     * details ( e.g. last activity ) through the login session token.
     *
     * @api_header string X-API-KEY
     * @api_param  string token
     * @return     object JSON
     */
    public function validate_session_get()
    {
        $this->is_authorized();

        if (! get('token')) {
            $this->response([
                'message' => $this->error_message('missing_token'),
                'status' => false
            ], self::HTTP_NOT_ACCEPTABLE);
        }

        $token = do_secure(get('token'));
        $session = $this->Tool_model->user_session_by_token($token);

        if (! empty($session)) {
            $this->User_model->update_user_activity_details(
                $this->db_config['site_timezone'],
                $session->user_id,
                $token
            );

            $this->response(['status' => true], self::HTTP_OK);
        }

        $this->response([
            'status' => false
        ], self::HTTP_NOT_ACCEPTABLE);
    }

    /**
     * User Login Session
     *
     * Use to reach to the user through the session token.
     *
     * @api_header string X-API-KEY
     * @api_param  string token
     * @return     object JSON
     */
    public function session_get()
    {
        $this->is_authorized();

        if (! get('token')) {
            $this->response([
                'message' => $this->error_message('missing_token'),
                'status' => false
            ], self::HTTP_NOT_ACCEPTABLE);
        }

        $token = do_secure(get('token'));
        $session = $this->Tool_model->user_session_by_token($token);

        if (empty($session)) {
            $this->response([
                'message' => $this->error_message('no_token_session'),
                'status' => false
            ], self::HTTP_NOT_ACCEPTABLE);
        }

        $this->response([
            'data' => $session,
            'status' => true
        ], self::HTTP_OK);
    }

    /**
     * User Registration
     *
     * @api_header string X-API-KEY
     * @api_param  string first_name
     * @api_param  string last_name
     * @api_param  string email_address
     * @api_param  string password
     * @return     object JSON
     */
    public function register_post()
    {
        $this->is_authorized();

        if ($this->form_validation->run('register_api')) {
            if ($this->db_config['u_enable_registration'] == 0) {
                $this->response([
                    'message' => $this->error_message('registration_disabled'),
                    'status' => false
                ], self::HTTP_NOT_ACCEPTABLE);
            }

            $status = validate_password(post('password'));

            if ($status['status'] === false) {
                $this->response([
                    'message' => $this->error_message($status['message']),
                    'status' => false
                ], self::HTTP_NOT_ACCEPTABLE);
            }

            $user = $this->User_model->get_by_email(do_secure(post('email_address')));

            if (! empty($user)) {
                $this->response([
                    'message' => $this->error_message('already_registered'),
                    'status' => false
                ], self::HTTP_NOT_ACCEPTABLE);
            }

            $data = [
                'first_name' => do_secure_u(post('first_name')),
                'last_name' => do_secure_u(post('last_name')),
                'email_address' => do_secure_l(post('email_address')),
                'password' => password_hash(post('password'), PASSWORD_DEFAULT),
                'restful_api_key' => get_short_random_string(),
                'role' => $this->db_config['u_default_user_role'],
                'registered_month_year' => get_site_date('n-Y'),
                'registered_at' => time()
            ];

            $source = "{$data['first_name']}{$data['last_name']}";

            if (! is_alpha_numeric($source) || strlen($source) < 5) {
                $source = cleaned_email_username($data['email_address']);
            }

            $data['username'] = $this->User_model->get_unique_username($source);

            $id = $this->User_model->add($data);

            if (! empty($id)) {
                log_user_activity('user_registered_api', '', $id);

                if (is_email_settings_filled()) {
                    send_welcome_email($id);
                    everification_setup($id);
                }

                $this->response([
                    'message' => $this->success_message('user_registered_api'),
                    'status' => true
                ], self::HTTP_OK);
            }

            $this->response([
                'message' => $this->error_message('went_wrong'),
                'status' => false
            ], self::HTTP_NOT_ACCEPTABLE);
        }

        $this->response([
            'message' => validation_errors(),
            'status' => false
        ], self::HTTP_NOT_ACCEPTABLE);
    }
    public function user_email_get()
    {
        $this->is_authorized();

        // Check if email is provided
        if (!get('email')) {
            $this->response([
                'message' => $this->error_message('missing_email'),
                'status' => false
            ], self::HTTP_NOT_ACCEPTABLE);
        }

        $email = do_secure_l(get('email'));

        // Fetch user by email
        $user = $this->User_model->get_by_email($email);

        if (empty($user)) {
            $this->response([
                'message' => $this->error_message('no_user_found'),
                'status' => false
            ], self::HTTP_NOT_ACCEPTABLE);
        }

        // Success
        $this->response([
            'data' => $user,
            'status' => true
        ], self::HTTP_OK);
    }

    /**
     * User Data
     *
     * @api_header string  X-API-KEY
     * @api_param  integer id
     * @return     object  JSON
     */
    public function user_get()
    {
        $this->is_authorized();

        if (! get('id')) {
            $this->response([
                'message' => $this->error_message('missing_user_id'),
                'status' => false
            ], self::HTTP_NOT_ACCEPTABLE);
        }

        $user_id = intval(get('id'));
        $user = $this->User_model->get_by_id($user_id);

        if (empty($user)) {
            $this->response([
                'message' => $this->error_message('no_user_found'),
                'status' => false
            ], self::HTTP_NOT_ACCEPTABLE);
        }

        $this->response([
            'data' => $user,
            'status' => true
        ], self::HTTP_OK);
    }

    /**
     * Logout/Invalidate User
     *
     * @api_header string X-API-KEY
     * @api_param  string token
     * @return     object JSON
     */
    public function logout_get()
    {
        $this->is_authorized();

        if (! get('token')) {
            $this->response([
                'message' => $this->error_message('missing_token'),
                'status' => false
            ], self::HTTP_NOT_ACCEPTABLE);
        }

        $token = do_secure(get('token'));
        $session = $this->Tool_model->user_session_by_token($token);
        $user_id = 0;

        if (! empty($session)) {
            $user_id = $session->user_id;

            $this->User_model->update_user([
                'is_online' => 0
            ], $user_id, false);
        } else {
            $this->response([
                'message' => $this->error_message('invalid_req'),
                'status' => false
            ], self::HTTP_NOT_ACCEPTABLE);
        }

        if (! $this->Tool_model->delete_user_session_by_token($token)) {
            $this->response([
                'message' => $this->error_message('user_logout_failed'),
                'status' => false
            ], self::HTTP_NOT_ACCEPTABLE);
        }

        log_user_activity('user_logged_out_api', '', $user_id);

        $this->response([
            'message' => $this->success_message('user_logged_out'),
            'status' => true
        ], self::HTTP_OK);
    }

    /**
     * Error Message
     *
     * @param  string $key Error messages language key without "err_" prefix.
     * @return string
     */
    private function error_message($key)
    {
        return err_lang($key);
    }

    /**
     * Success Message
     *
     * @param  string $key Success messages language key without "suc_" prefix.
     * @return string
     */
    private function success_message($key)
    {
        return suc_lang($key);
    }

    /**
     * Adjust Cash Balance (Add or Cut Cash)
     *
     * @api_header string X-API-KEY
     * @api_param  integer user_id
     * @api_param  float   amount
     * @api_param  string  type   ("add_cash" or "cut_cash")
     * @api_param  string  gateway (optional)
     * @return     object  JSON
     */
    public function adjust_cash_balance_post()
    {
        $this->is_authorized(); // Only master API user allowed (id = 1)

        $user_id = intval($this->input->post('user_id'));
        $amount = floatval($this->input->post('amount'));
        $type = do_secure($this->input->post('type')); // "add_cash" or "cut_cash"
        $gateway = $this->input->post('gateway') ?: 'manual';

        // Validate input
        if (empty($user_id) || empty($amount) || empty($type)) {
            return $this->response([
                'message' => $this->error_message('invalid_req'),
                'status' => false
            ], self::HTTP_NOT_ACCEPTABLE);
        }

        if (!in_array($type, ['add_cash', 'cut_cash'])) {
            return $this->response([
                'message' => $this->error_message('invalid_req'),
                'status' => false
            ], self::HTTP_NOT_ACCEPTABLE);
        }

        // Fetch user
        $user = $this->User_model->get_by_id_cash($user_id);
        if (empty($user)) {
            return $this->response([
                'message' => $this->error_message('no_user_found'),
                'status' => false
            ], self::HTTP_NOT_ACCEPTABLE);
        }

        $current_balance = doubleval($user->credit);

        // Prevent negative
        if ($type === 'cut_cash' && ($current_balance - $amount) < 0) {
            return $this->response([
                'message' => 'Insufficient balance to cut',
                'status' => false
            ], self::HTTP_NOT_ACCEPTABLE);
        }

        // Perform update
        if ($type === 'add_cash') {
            $status = $this->User_model->add_cash_balance($user_id, $amount);
        } else {
            $status = $this->User_model->cut_cash_balance($user_id, $amount);
        }

        if ($status) {
            $this->load->model('Payment_model');

            // Log to payment table
            $data = [
                'user_id' => $user_id,
                'item_name' => ucwords(str_replace('_', ' ', $type)),
                'visible_to_user' => 1,
                'create_invoice' => 0,
                'gateway' => $gateway,
                'amount' => $amount,
                'currency_id' => 0,
            ];
            $this->Payment_model->log_adjust_balance($data);

            log_user_activity('cash_balance_adjusted_api', $user_id);

            return $this->response([
                'message' => 'Cash balance updated successfully',
                'status' => true,
                'new_balance' => ($type === 'add_cash')
                    ? ($current_balance + $amount)
                    : ($current_balance - $amount)
            ], self::HTTP_OK);
        }

        return $this->response([
            'message' => $this->error_message('went_wrong'),
            'status' => false
        ], self::HTTP_NOT_ACCEPTABLE);
    }
    /**
     * Get User Cash Balance
     *
     * @api_header string X-API-KEY
     * @api_param  integer user_id
     * @return     object JSON
     */
    public function get_cash_balance_get()
    {
        $this->is_authorized();

        $user_id = intval($this->input->get('user_id'));
        if (empty($user_id)) {
            return $this->response([
                'message' => $this->error_message('missing_user_id'),
                'status' => false
            ], self::HTTP_NOT_ACCEPTABLE);
        }

        // Get user's cash info (make sure this method includes `credit` column)
        $user = $this->User_model->get_cash_by_user_id($user_id);
        if (empty($user)) {
            return $this->response([
                'message' => $this->error_message('no_user_found'),
                'status' => false
            ], self::HTTP_NOT_ACCEPTABLE);
        }

        return $this->response([
            'message' => 'Cash balance Fetched successfully',
            'status' => true,
            'data' => [
                'user_id' => $user->id,
                'username' => $user->username,
                'balance' => floatval($user->credit),
            ],
            'status' => true
        ], self::HTTP_OK);
    }

    /**
     * Adjust Cash Balance (Add or Cut Cash)
     *
     * @api_header string X-API-KEY
     * @api_param  integer user_id
     * @api_param  float   amount
     * @api_param  string  type   ("add_cash" or "cut_cash")
     * @api_param  string  gateway (optional)
     * @return     object  JSON
     */
    public function adjust_cash_balance_2_post()
    {
        $this->is_authorized(); // Only master API user allowed (id = 1)

        $user_id = intval($this->input->post('user_id'));
        $amount = floatval($this->input->post('amount'));
        $type = do_secure($this->input->post('type')); // "add_cash" or "cut_cash"
        $gateway = $this->input->post('gateway') ?: 'manual';

        // Validate input
        if (empty($user_id) || empty($amount) || empty($type)) {
            return $this->response([
                'message' => $this->error_message('invalid_req'),
                'status' => false
            ], self::HTTP_NOT_ACCEPTABLE);
        }

        if (!in_array($type, ['add_cash', 'cut_cash'])) {
            return $this->response([
                'message' => $this->error_message('invalid_req'),
                'status' => false
            ], self::HTTP_NOT_ACCEPTABLE);
        }

        // Fetch user
        $user = $this->User_model->get_by_id($user_id);
        if (empty($user)) {
            return $this->response([
                'message' => $this->error_message('no_user_found'),
                'status' => false
            ], self::HTTP_NOT_ACCEPTABLE);
        }

        $current_balance = doubleval($user->cash_balance);

        // Prevent negative
        if ($type === 'cut_cash' && ($current_balance - $amount) < 0) {
            return $this->response([
                'message' => 'Insufficient balance to cut',
                'status' => false
            ], self::HTTP_NOT_ACCEPTABLE);
        }

        // Perform update
        if ($type === 'add_cash') {
            $status = $this->User_model->add_cash_balance($user_id, $amount);
        } else {
            $status = $this->User_model->cut_cash_balance($user_id, $amount);
        }

        if ($status) {
            $this->load->model('Payment_model');

            // Log to payment table
            $data = [
                'user_id' => $user_id,
                'item_name' => ucwords(str_replace('_', ' ', $type)),
                'visible_to_user' => 1,
                'create_invoice' => 0,
                'gateway' => $gateway,
                'amount' => $amount,
                'currency_id' => 0,
            ];
            $this->Payment_model->log_adjust_balance($data);

            log_user_activity('cash_balance_adjusted_api', $user_id);

            return $this->response([
                'message' => 'Cash balance updated successfully',
                'status' => true,
                'new_balance' => ($type === 'add_cash')
                    ? ($current_balance + $amount)
                    : ($current_balance - $amount)
            ], self::HTTP_OK);
        }

        return $this->response([
            'message' => $this->error_message('went_wrong'),
            'status' => false
        ], self::HTTP_NOT_ACCEPTABLE);
    }
}
