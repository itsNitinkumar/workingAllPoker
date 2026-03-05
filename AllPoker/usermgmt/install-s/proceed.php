<?php

/**
 * Proceed
 *
 * @author Shahzaib
 */

error_reporting(null);

require_once 'includes/errors.php';
require_once 'includes/functions.php';
require_once 'includes/MysqliDb.php';

if (! empty($_GET['step'])) {
    $step = strtolower($_GET['step']);

    if ($step === 'requirements') {
        if (! verify_requirements()) error('requirements');

        jump('permissions');
    } else if ($step === 'permissions') {
        if (! verify_permissions()) error('permissions');

        jump('install');
    } else if ($step === 'install') {
        if (
            post('purchase_code')
            && post('database_hostname')
            && post('database_name')
            && post('database_username')
            && post('user_email')
            && post('user_password')
        ) {
            $purchase_code     = do_clean(post('purchase_code'));
            $database_hostname = do_clean(post('database_hostname'));
            $database_name     = do_clean(post('database_name'));
            $database_username = do_clean(post('database_username'));
            $database_password = do_clean(post('database_password'));
            $user_email        = do_clean(post('user_email'));
            $grabbed           = '';

            try {
                $mysqli = new mysqli($database_hostname, $database_username, $database_password, $database_name);

                if (mysqli_connect_error()) error('db_connect');
            } catch (Exception $e) {
                error('db_connect');
            }

            if (! filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
                error('invalid_email');
            }

            if (strlen(post('user_password')) < 6) {
                error('invalid_pass');
            }

            $api_request_url = 'https://envato.zaib.pk/api.php?target=ca743b568a9695b40f040ea094be4297';
            $api_request_url .= "&purchase_code={$purchase_code}&product_order=2";
            $api_request_url .= '&website=' . base_url();

            // $response = send_get_curl($api_request_url);

            // if (empty($response) || empty($response->status)) error('went_wrong');

            // if ($response->status == 'false') d_error($response->message);

            $mysqlidb = new MysqliDb($mysqli);
            $sql_lines = file('database/zum.sql');

            foreach ($sql_lines as $sql_line) {
                $query = false;

                if (substr(do_clean($sql_line), 0, 2) === '--' || empty($sql_line)) {
                    continue;
                }

                $grabbed .= $sql_line;

                if (substr(do_clean($sql_line), -1, 1) === ';') {
                    $query = $mysqli->query($grabbed);
                    $grabbed = '';
                }
            }

            if ($query) {
                $mysqlidb->where('id', 1);
                $mysqlidb->update(
                    'users',
                    [
                        'password' => password_hash(post('user_password'), PASSWORD_DEFAULT),

                        // Make sure that your database file ( installer ) must have `restful_api_key`
                        // column for the `users` table. If you've downloaded the latest copy of
                        // databae file, then you don't have to be worry about it.
                        'restful_api_key' => md5(time()),

                        'email_address' => $user_email,
                        'registered_month_year' => date('n-Y'),
                        'registered_at' => time()
                    ]
                );

                $processing_file = 'database/config.php';
                $target_file = '../application/config/database.php';

                copy($processing_file, $target_file);

                $database_file = file_get_contents($processing_file);
                $config = str_replace('{DATABASE_HOSTNAME}', $database_hostname, $database_file);
                $config = str_replace('{DATABASE_USERNAME}', $database_username, $config);
                $config = str_replace('{DATABASE_PASSWORD}', $database_password, $config);
                $config = str_replace('{DATABASE_NAME}', $database_name, $config);

                if (! file_put_contents($target_file, $config)) {
                    error('db_config_write');
                }

                jump('finishing');
            }

            error('db_queries');
        }

        error('input');
    }
}
