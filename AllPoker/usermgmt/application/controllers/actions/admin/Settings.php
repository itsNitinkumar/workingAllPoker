<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Settings Controller ( Admin, Actions )
 *
 * @author Shahzaib
 */
class Settings extends MY_Controller
{

    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        if (! $this->zuser->is_logged_in) r_s_jump('login');

        if (! $this->zuser->has_permission('settings')) {
            r_error_no_permission();
        }

        $this->load->library('form_validation');
    }

    /**
     * General Settings Page Input Handling.
     *
     * @return void
     */
    public function general()
    {
        if ($this->form_validation->run('settings_general')) {
            $data = [
                'enable_newsletter' => only_binary(post('enable_newsletter')),
                'site_show_cookie_popup' => only_binary(post('site_show_cookie_popup')),
                'dashboard_cache_time' => intval(post('dashboard_cache_time')),
                'enable_restful_api' => only_binary(post('enable_restful_api')),
                'captcha_plugin' => do_secure_l(post('captcha_plugin')),
                'site_name' => do_secure(post('site_name')),
                'site_theme' => do_secure_l(post('site_theme')),
                'site_timezone' => do_secure(post('site_timezone')),
                'site_tagline' => do_secure(post('site_tagline')),
                'site_about' => do_secure(post('site_about')),
                'site_description' => do_secure(post('site_description')),
                'site_keywords' => do_secure(post('site_keywords')),
                'mm_allowed_ips' => do_secure(post('mm_allowed_ips'), true),
                'maintenance_mode' => only_binary(post('maintenance_mode')),
                'mm_message' => do_secure(post('mm_message')),
                'custom_css' => do_secure(post('custom_css'), true),
                'custom_js' => post('custom_js'),
                'facebook_link' => do_secure_url(post('facebook_link')),
                'twitter_link' => do_secure_url(post('twitter_link')),
                'linkedin_link' => do_secure_url(post('linkedin_link')),
                'youtube_link' => do_secure_url(post('youtube_link'))
            ];

            if (! array_key_exists($data['site_theme'], SITE_THEMES)) r_error('invalid_req');

            $ips_arr = explode(PHP_EOL, $data['mm_allowed_ips']);
            $ips_arr = array_map('trim', $ips_arr);

            foreach ($ips_arr as $ip) {
                if (! empty($ip) && ! filter_var($ip, FILTER_VALIDATE_IP)) r_error('review_ip');
            }

            if (! in_array($this->input->ip_address(), $ips_arr) && $data['maintenance_mode'] == 1) {
                $data['mm_allowed_ips'] .= PHP_EOL . $this->input->ip_address();
            }

            if (! empty($_FILES['site_logo']['tmp_name']) || ! empty($_FILES['site_favicon']['tmp_name'])) {
                $this->load->library('ZFiles');

                if (! empty($_FILES['site_favicon']['tmp_name'])) {
                    $old_file = db_config('site_favicon');
                    $data['site_favicon'] = $this->zfiles->upload_image_file('site_favicon', 'general');
                    $this->zfiles->delete_image_file('general', $old_file);
                }

                if (! empty($_FILES['site_logo']['tmp_name'])) {
                    $old_file = db_config('site_logo');
                    $data['site_logo'] = $this->zfiles->upload_image_file('site_logo', 'general');
                    $this->zfiles->delete_image_file('general', $old_file);
                }
            }

            if ($data['maintenance_mode'] == 1 && empty($data['mm_message'])) {
                r_error('missing_mm_message');
            }

            $keys = ['facebook_link', 'twitter_link', 'linkedin_link', 'youtube_link'];

            if (! is_valid_urls_post($keys)) {
                r_error('invalid_urls');
            }

            $this->Setting_model->update_options($data);
            log_user_activity('general_settings_updated');
            r_settings('general');
        }

        d_r_error(validation_errors());
    }

    /**
     * Delete Site Logo
     *
     * @return void
     */
    public function delete_site_logo()
    {
        $this->load->library('ZFiles');
        $this->zfiles->delete_image_file('general', db_config('site_logo'));
        $this->Setting_model->update_options(['site_logo' => '']);

        log_user_activity('site_logo_deleted');
        r_settings('general');
    }

    /**
     * Support Settings Page Input Handling.
     *
     * @return void
     */
    public function support()
    {
        if ($this->form_validation->run('settings_support')) {
            $data = [
                'sp_notify_replies' => only_binary(post('sp_notify_replies')),
                'sp_enable_tickets' => only_binary(post('sp_enable_tickets')),
                'cu_enable_form' => only_binary(post('cu_enable_form')),
                'cu_email_address' => do_secure_l(post('cu_email_address'))
            ];

            $this->Setting_model->update_options($data);
            log_user_activity('support_settings_updated');
            r_settings('support');
        }

        d_r_error(form_error('cu_email_address'));
    }

    /**
     * Users Settings Page Input Handling.
     *
     * @return void
     */
    public function users()
    {
        $data = [
            'u_2f_authentication' => intval(post('u_2f_authentication')),
            'u_temporary_lockout' => do_secure_l(post('u_temporary_lockout')),
            'u_lockout_unlock_time' => intval(post('u_lockout_unlock_time')),
            'u_enable_registration' => only_binary(post('u_enable_registration')),
            'u_send_welcome_email' => only_binary(post('u_send_welcome_email')),
            'u_default_user_role' => intval(post('u_default_user_role')),
            'u_max_avator_size' => do_secure_l(post('u_max_avator_size')),
            'u_password_requirement' => do_secure_l(post('u_password_requirement')),
            'u_req_ev_onchange' => only_binary(post('u_req_ev_onchange')),
            'u_allow_email_change' => only_binary(post('u_allow_email_change')),
            'u_allow_username_change' => only_binary(post('u_allow_username_change')),
            'u_reset_password' => only_binary(post('u_reset_password')),
            'u_can_remove_them' => only_binary(post('u_can_remove_them')),
            'u_track_activities' => only_binary(post('u_track_activities')),
            'u_notify_pass_changed' => only_binary(post('u_notify_pass_changed'))
        ];

        if (! empty($data['u_max_avator_size'])) {
            if (! preg_match('/^[0-9]+x[0-9]+$/', $data['u_max_avator_size'])) {
                r_error('invalid_avatar_size');
            }
        }

        $this->Setting_model->update_options($data);
        log_user_activity('users_settings_updated');
        r_settings('users');
    }

    /**
     * Add Role Input Handling.
     *
     * @return void
     */
    public function add_role()
    {
        if (! $this->zuser->has_permission('roles_and_permissions')) {
            r_error_no_permission();
        }

        if ($this->form_validation->run('settings_role_permission')) {
            $access_key = do_secure(post('access_key'));
            $data = ['name' => do_secure(post('name')), 'access_key' => $access_key];

            if (! is_valid_access_key($access_key)) {
                r_error('invalid_ak');
            }

            if ($this->Setting_model->is_role_exists($access_key)) {
                r_error('role_exists');
            }

            $id = $this->Setting_model->add_role($data);

            if (! empty($id)) {
                $data['id'] = $id;

                $html = read_view('admin/responses/add_role', $data);

                log_user_activity('role_added', $id);
                r_success_add($html);
            }

            r_error('went_wrong');
        }

        d_r_error(validation_errors());
    }

    /**
     * Edit Role ( Response )
     *
     * @return void
     */
    public function edit_role()
    {
        if (! $this->zuser->has_permission('roles_and_permissions')) {
            r_error_no_permission();
        }

        if (empty(post('id'))) r_error('invalid_req');

        $data = $this->Setting_model->role(intval(post('id')));

        if (! empty($data)) {
            display_view('admin/responses/forms/edit_role', $data);
        }

        r_error('invalid_req');
    }

    /**
     * Update Role Input Handling.
     *
     * @return void
     */
    public function update_role()
    {
        if (! $this->zuser->has_permission('roles_and_permissions')) {
            r_error_no_permission();
        }

        if ($this->form_validation->run('settings_role_permission')) {
            $id = intval(post('id'));
            $access_key = do_secure(post('access_key'));
            $data['name'] = do_secure(post('name'));

            $role = $this->Setting_model->role($id);

            if (empty($role)) r_error('invalid_req');

            if (! is_valid_access_key($access_key)) r_error('invalid_ak');

            if ($this->Setting_model->is_role_exists($access_key, $id)) {
                r_error('role_exists');
            }

            if ($role->is_built_in == 0) {
                $data['access_key'] = $access_key;
            }

            if ($this->Setting_model->update_role($data, $id)) {
                $role->access_key = $access_key;
                $role->name = $data['name'];

                $html = read_view('admin/responses/update_role', $role);

                log_user_activity('role_updated', $id);
                r_success_replace($id, $html);
            }

            r_error('not_updated');
        }

        d_r_error(validation_errors());
    }

    /**
     * Checked Permissions
     *
     * @param  integer $role_id
     * @return array
     */
    private function checked_permissions($role_id)
    {
        $permissions = $this->Setting_model->permissions();

        $ids = [];

        foreach ($permissions as $permission) {
            $id = html_escape($permission->id);

            // Add the permissions IDs in the array "that is checked
            // by the user" or "the built-in permissions for the default
            // role ( Super Admin ) permissions request:
            if (
                post('perm_' . $id) ||
                ($permission->is_built_in == 1 && $role_id == 1)
            ) {
                $ids[] = $id;
            }
        }

        return $ids;
    }

    /**
     * Update Role Permissions Input Handling.
     *
     * @return void
     */
    public function update_role_permissions()
    {
        if (! $this->zuser->has_permission('roles_and_permissions')) {
            r_error_no_permission();
        }

        $role_id = intval(post('role_id'));

        if (empty($this->Setting_model->role($role_id))) r_error('invalid_req');

        $ids = $this->checked_permissions($role_id);

        if (! empty($ids)) {
            $this->Setting_model->delete_role_permissions_ni($role_id, $ids);

            foreach ($ids as $id) {
                if (! $this->Setting_model->role_has_permission($role_id, $id)) {
                    $this->Setting_model->assign_permission($role_id, $id);
                }
            }
        } else {
            $this->Setting_model->delete_role_permissions($role_id);
        }

        log_user_activity('role_permissions_updated', $role_id);
        r_s_jump("admin/settings/roles/{$role_id}", 'updated');
    }

    /**
     * Delete Role
     *
     * @return void
     */
    public function delete_role()
    {
        if (! $this->zuser->has_permission('roles_and_permissions')) {
            r_error_no_permission();
        }

        $id = intval(post('id'));

        $data = $this->Setting_model->role($id);

        if (! empty($data)) {
            if ($data->is_built_in == 1) r_error('invalid_req');

            if ($this->Setting_model->delete_role($id)) {
                $this->Setting_model->delete_role_permissions($id);

                log_user_activity('role_deleted', $id);
                r_success_remove($id);
            }
        }

        r_error('invalid_req');
    }

    /**
     * Add Permission Input Handling.
     *
     * @return void
     */
    public function add_permission()
    {
        if (! $this->zuser->has_permission('roles_and_permissions')) {
            r_error_no_permission();
        }

        if ($this->form_validation->run('settings_role_permission')) {
            $access_key = do_secure(post('access_key'));
            $data = ['name' => do_secure(post('name')), 'access_key' => $access_key];

            if (! is_valid_access_key($access_key)) {
                r_error('invalid_ak');
            }

            if ($this->Setting_model->is_permission_exists($access_key)) {
                r_error('permission_exists');
            }

            $id = $this->Setting_model->add_permission($data);

            if (! empty($id)) {
                $data['id'] = $id;

                $html = read_view('admin/responses/add_permission', $data);

                log_user_activity('permission_added', $id);
                r_success_add($html);
            }

            r_error('went_wrong');
        }

        d_r_error(validation_errors());
    }

    /**
     * Edit Permission ( Response )
     *
     * @return void
     */
    public function edit_permission()
    {
        if (! $this->zuser->has_permission('roles_and_permissions')) {
            r_error_no_permission();
        }

        if (! post('id')) r_error('invalid_req');

        $data = $this->Setting_model->permission(intval(post('id')));

        if (! empty($data)) {
            display_view('admin/responses/forms/edit_permission', $data);
        }

        r_error('invalid_req');
    }

    /**
     * Update Permission Input Handling.
     *
     * @return void
     */
    public function update_permission()
    {
        if (! $this->zuser->has_permission('roles_and_permissions')) {
            r_error_no_permission();
        }

        if ($this->form_validation->run('settings_role_permission')) {
            $access_key = do_secure(post('access_key'));
            $data['name'] = do_secure(post('name'));
            $id = intval(post('id'));

            $permission = $this->Setting_model->permission($id);

            if (empty($permission)) r_error('invalid_req');

            if (! is_valid_access_key($access_key)) r_error('invalid_ak');

            if ($this->Setting_model->is_permission_exists($access_key, $id)) {
                r_error('permission_exists');
            }

            if ($permission->is_built_in == 0) {
                $data['access_key'] = $access_key;
            }

            if ($this->Setting_model->update_permission($data, $id)) {
                $permission->access_key = $access_key;
                $permission->name = $data['name'];

                $html = read_view('admin/responses/update_permission', $permission);

                log_user_activity('permission_updated', $id);
                r_success_replace($id, $html);
            }

            r_error('not_updated');
        }

        d_r_error(validation_errors());
    }

    /**
     * Delete Permission
     *
     * @return void
     */
    public function delete_permission()
    {
        if (! $this->zuser->has_permission('roles_and_permissions')) {
            r_error_no_permission();
        }

        $id = intval(post('id'));

        $data = $this->Setting_model->permission($id);

        if (! empty($data)) {
            if ($data->is_built_in == 1) r_error('invalid_req');

            if ($this->Setting_model->delete_permission($id)) {
                log_user_activity('permission_deleted', $id);
                r_success_remove($id);
            }
        }

        r_error('invalid_req');
    }

    /**
     * Payment Settings Page Input Handling.
     *
     * @return void
     */
    public function payment()
    {
        $data = [
            'credit_pay_enable' => only_binary(post('credit_pay_enable')),
            'iv_company_name' => do_secure(post('iv_company_name')),
            'iv_phone_number' => do_secure(post('iv_phone_number')),
            'iv_address_1' => do_secure(post('iv_address_1')),
            'iv_address_2' => do_secure(post('iv_address_2'))
        ];

        $this->Setting_model->update_options($data);
        log_user_activity('payment_settings_updated');
        r_settings('payment');
    }

    /**
     * APIs Settings Page Input Handling.
     *
     * @return void
     */
    public function apis()
    {
        $data = [
            'fb_app_id' => do_secure(post('fb_app_id')),
            'fb_app_secret' => do_secure(post('fb_app_secret')),
            'fb_enable_login' => only_binary(post('fb_enable_login')),
            'gl_client_key' => do_secure(post('gl_client_key')),
            'gl_secret_key' => do_secure(post('gl_secret_key')),
            'gl_enable' => only_binary(post('gl_enable')),
            'gr_public_key' => do_secure(post('gr_public_key')),
            'gr_secret_key' => do_secure(post('gr_secret_key')),
            'gr_enable' => only_binary(post('gr_enable')),

            // Version 2.5:
            'cloudflare_turnstile_site_key' => do_secure(post('cloudflare_turnstile_site_key')),
            'cloudflare_turnstile_secret_key' => do_secure(post('cloudflare_turnstile_secret_key')),
            'cloudflare_turnstile_enable' => only_binary(post('cloudflare_turnstile_enable')),

            // Version 2.6:
            'hcaptcha_site_key' => do_secure(post('hcaptcha_site_key')),
            'hcaptcha_secret_key' => do_secure(post('hcaptcha_secret_key')),
            'hcaptcha_enable' => only_binary(post('hcaptcha_enable')),

            'google_analytics_id' => do_secure(post('google_analytics_id')),
            'tw_consumer_key' => do_secure(post('tw_consumer_key')),
            'tw_consumer_secret' => do_secure(post('tw_consumer_secret')),
            'tw_enable_login' => only_binary(post('tw_enable_login')),
            'sp_publishable_key' => do_secure(post('sp_publishable_key')),
            'sp_secret_key' => do_secure(post('sp_secret_key')),
            'sp_enable' => only_binary(post('sp_enable')),
            'ipinfo_token' => do_secure(post('ipinfo_token')),
            'vkontakte_app_id' => do_secure(post('vkontakte_app_id')),
            'vkontakte_secret_key' => do_secure(post('vkontakte_secret_key')),
            'vkontakte_enable' => only_binary(post('vkontakte_enable'))
        ];

        $this->Setting_model->update_options($data);
        log_user_activity('apis_settings_updated');
        r_settings('apis');
    }

    /**
     * Email Settings Page Input Handling.
     *
     * @return void
     */
    public function email()
    {
        $fv_key = 'settings_email_smtp';

        if (post('e_protocol') === 'mail') $fv_key = 'settings_email_mail';

        if ($this->form_validation->run($fv_key)) {
            $data = [
                'e_sender_name' => do_secure(post('e_sender_name')),
                'e_sender' => do_secure(post('e_sender')),
                'e_protocol' => do_secure(post('e_protocol'))
            ];

            if (post('e_protocol') === 'smtp') {
                $data['e_host'] = do_secure(post('e_host'));
                $data['e_username'] = do_secure(post('e_username'));
                $data['e_password'] = do_secure(post('e_password'));
                $data['e_encryption'] = do_secure(post('e_encryption'));
                $data['e_port'] = intval(post('e_port'));
            }

            $this->Setting_model->update_options($data);
            log_user_activity('email_settings_updated');
            r_settings('email');
        }

        d_r_error(validation_errors());
    }

    /**
     * Test Email Settings Input Handling.
     *
     * @return void
     */
    public function test_email_settings()
    {
        if (! is_email_settings_filled()) r_error('missing_email_config_a');

        if ($this->form_validation->run('just_email_address') === true) {
            $this->load->library('ZMailer');
            $this->lang->load('email', 'english');

            $receiver = do_secure_l(post('email_address'));
            $subject = lang('e_test_email_subject');
            $message = lang('e_test_email_message');
            $status = $this->zmailer->send_email($receiver, $subject, $message, true);

            if ($status === true) {
                log_user_activity('email_settings_tested');
                r_success('esettings_tested');
            }

            d_r_error($status);
        }

        d_r_error(form_error('email_address'));
    }
}
