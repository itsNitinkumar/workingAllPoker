<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

$lang['text_rest_invalid_api_key']       = 'Invalid API Key %s'; // %s is the REST API key
$lang['text_rest_invalid_credentials']   = 'Invalid Credentials';
$lang['text_rest_ip_denied']             = 'IP Denied';
$lang['text_rest_ip_unauthorized']       = 'IP Unauthorized';
$lang['text_rest_unauthorized']          = 'Unauthorized';
$lang['text_rest_ajax_only']             = 'Only AJAX requests are allowed.';
$lang['text_rest_api_key_unauthorized']  = 'This API key does not have access to the requested controller.';
$lang['text_rest_api_key_permissions']   = 'This API key does not have enough permissions.';
$lang['text_rest_api_key_time_limit']    = 'This API key has reached the time limit for this method.';
$lang['text_rest_ip_address_time_limit'] = 'This IP Address has reached the time limit for this method.';
$lang['text_rest_unknown_method']        = 'Unknown Method';
$lang['text_rest_unsupported']           = 'Unsupported Protocol';

// Custom Messages:
$lang['av_user_logged_in_api']           = 'Logged in Through the API';
$lang['av_user_registered_api']          = 'Registered Through the API';
$lang['av_user_logged_out_api']          = 'Logged Out Through the API';

$lang['err_api_module_disabled']         = 'API Module is Disabled.';
$lang['err_no_token_session']            = 'Sorry, no session found for this token.';
$lang['err_no_user_found']               = 'Sorry, no user found for this ID.';
$lang['err_missing_user_id']             = 'Missing User ID';
$lang['err_missing_token']               = 'Missing Token';
$lang['err_user_logout_failed']          = 'Failed to logout, please try again later.';
$lang['err_api_holder']                  = 'The API holder account status must be active.';
$lang['err_no_permission']               = 'Sorry, you do not have permission of this module.';

$lang['suc_user_logged_in_api']          = 'Successfully Authenticated';
$lang['suc_user_registered_api']         = 'Successfully registered, you can login now.';
$lang['suc_user_logged_out']             = 'Successfully Logged Out';
