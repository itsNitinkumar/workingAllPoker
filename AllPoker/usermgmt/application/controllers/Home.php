<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Home Controller
 *
 * @author Shahzaib
 */
class Home extends MY_Controller
{

    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->area = 'home';
    }

    /**
     * Index Page
     *
     * @return void
     */
    public function index()
    {
        $data['meta_description'] = db_config('site_description');
        $data['meta_keywords'] = db_config('site_keywords');
        $data['view'] = 'welcome';

        $this->load_template($data);
    }

    /**
     * Page ( Terms of Use, Privacy Policy, Custom ).
     *
     * @param  mixed  $value
     * @param  string $source
     * @return void
     */
    public function page($value = 0, $source = 'built-in')
    {
        $value = do_secure($value);

        $this->load->model('Page_model');

        if ($source === 'custom') {
            $visibility = 1;

            if ($this->zuser->has_permission('pages')) {
                $visibility = null;
            }

            $page = $this->Page_model->custom_page($value, 'slug', $visibility);
        } else {
            $page = $this->Page_model->page($value);

            $page->name = get_page_name($value);
        }

        if (empty($page)) show_404();

        $data['data']['page'] = $page;
        $data['meta_description'] = $page->meta_description;
        $data['meta_keywords'] = $page->meta_keywords;
        $data['title'] = $page->name;
        $data['view'] = 'page';

        $this->load_template($data);
    }

    /**
     * Custom Pages Page
     *
     * @param   string $slug
     * @return  void
     * @version 1.5
     */
    public function custom_page($slug = '')
    {
        $this->page($slug, 'custom');
    }

    /**
     * Newsletter Subscribe Form Input Handling ( Action ).
     *
     * @return void
     */
    public function subscribe()
    {
        if (db_config('enable_newsletter') == 0) r_error('temp_disabled');
        else if (! is_email_settings_filled()) r_error('missing_email_config');

        $this->load->library('form_validation');

        if ($this->form_validation->run('just_email_address')) {
            $this->load->model('Subscriber_model');

            $email_address = do_secure_l(post('email_address'));

            if (! $this->Subscriber_model->subscriber($email_address)) {
                $this->load->library('ZMailer');

                $authentication_token = get_short_random_string();
                $url_confirmation = env_url("confirm_subscription/{$authentication_token}");
                $url_unsubscribe = env_url("unsubscribe/{$authentication_token}");

                $template = $this->Tool_model->email_template_by_hook_and_lang('subscribe', get_language());

                if (empty($template)) r_error('missing_template');

                $message = replace_placeholders($template->template, [
                    '{SUB_LINK}' => $url_confirmation,
                    '{UNSUB_LINK}' => $url_unsubscribe,
                    '{SITE_NAME}' => db_config('site_name')
                ]);

                $data = [
                    'email_address' => $email_address,
                    'authentication_token' => $authentication_token,
                    'subscribed_at' => time()
                ];

                if ($this->zmailer->send_email($data['email_address'], $template->subject, $message)) {
                    if ($this->Subscriber_model->add($data)) {
                        log_user_activity('newsletter_subscribed');
                        r_success('nl_subscribed');
                    }

                    r_error('failed_subscribe_nl');
                }

                r_error('failed_email');
            }

            r_error('already_nl_subscribed');
        }

        d_r_error(form_error('email_address'));
    }

    /**
     * Newsletter Confirm Subscription ( Action ).
     *
     * @param  string $token
     * @return void
     */
    public function confirm_subscription($token = '')
    {
        $this->load->model('Subscriber_model');

        $token = do_secure($token);

        if ($this->Subscriber_model->verify_uc_by_token($token)) {
            if ($this->Subscriber_model->confirm($token)) {
                log_user_activity('nl_subscription_confirmed');
                success_redirect('nl_confirmed_sub');
            }

            error_redirect('went_wrong');
        }

        error_redirect('invalid_conf_sub_nl');
    }

    /**
     * Newsletter Unsubscribe ( Action ).
     *
     * @param  string $token
     * @return void
     */
    public function unsubscribe($token = '')
    {
        $this->load->model('Subscriber_model');

        $token = do_secure($token);

        if ($this->Subscriber_model->verify_by_token($token)) {
            if ($this->Subscriber_model->delete_by_token($token)) {
                log_user_activity('newsletter_unsubscribed');
                success_redirect('nl_unsubscribed');
            }

            error_redirect('went_wrong');
        }

        error_redirect('invalid_nl_unsub_token');
    }

    /**
     * Contact Us Form Input Handling ( Action ).
     *
     * @return void
     */
    public function send_message()
    {
        if (db_config('cu_enable_form') == 0) r_error('temp_disabled');

        $this->load->library('form_validation');

        if ($this->form_validation->run('contact_us')) {
            $this->load->model('Support_model');

            $go = submit_captcha();

            if (! $go) r_error_c('captcha');

            $data = [
                'full_name' => do_secure(post('full_name')),
                'email_address' => do_secure_l(post('email_address')),
                'message' => do_secure(post('message'), true),
                'received_at' => time()
            ];

            if ($this->Support_model->add_contact_message($data)) {
                if (! empty(db_config('cu_email_address'))) {
                    $this->lang->load('email', 'english');

                    $receiver = db_config('cu_email_address');
                    $subject = sprintf(lang('e_contact_us_subject'), $data['full_name']);

                    $message = sprintf(
                        lang('e_contact_us_message'),
                        $data['email_address'],
                        $data['message']
                    );

                    if (is_email_settings_filled()) {
                        $this->load->library('ZMailer');
                        $this->zmailer->send_email($receiver, $subject, $message);
                    }
                }

                log_user_activity('sent_contact_msg');
                r_success_gr('contact_msg_sent');
            }

            r_error_c('went_wrong');
        }

        d_r_error_c(validation_errors());
    }
}
