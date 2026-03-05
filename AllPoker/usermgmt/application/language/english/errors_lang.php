<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

// General:
$lang['err_token_expired']          = 'The token is expired, you have to request for the change of password again.';
$lang['err_invalid_token']          = 'The authentication token is invalid or used.';
$lang['err_not_updated']            = 'Seems you\'ve not changed the data.';
$lang['err_invalid_req']            = 'The received request is invalid.';
$lang['err_went_wrong']             = 'Something went wrong, please try again later.';
$lang['err_invalid_input']          = 'Invalid input, please review your fields.';
$lang['err_temp_disabled']          = 'Sorry, the requested action is temporarily disabled.';
$lang['err_missing_keys']           = 'Missing related API key(s), please review the settings.';
$lang['err_failed_email']           = 'Email sent failed, there can be some issue in the configuration.';
$lang['err_missing_input']          = 'Missing input, please review the required (*) fields.';
$lang['err_invalid_urls']           = 'Invalid URL(s) found, please review the related fields.';
$lang['err_slug_exists']            = 'This slug must be unique for each post.';
$lang['err_missing_email_config']   = 'The mailing part is not configured yet, please contact the site administrator.';
$lang['err_missing_email_config_a'] = 'The mailing part is not configured yet.';
$lang['err_401']                    = '401 - Sorry, you request cannot be processed because of unauthorization.';
$lang['err_403']                    = '403 - Sorry, the server is refused to accept your request.';
$lang['err_404']                    = '404 - Sorry, the requested file is not found on the server.';
$lang['err_500']                    = '500 - Sorry, your request cannot be processed because of technical issues.';
$lang['err_502']                    = '502 - Sorry, the server is not able to handle this request.';
$lang['err_503']                    = '503 - Sorry, currently the server is not able to handle this request.';

// Users:
$lang['err_not_logged_in']          = 'Account login is required to perform this action.';
$lang['err_req_logged_in']          = 'You are not logged in, please login to continue.';
$lang['err_already_registered']     = 'The user with this email address is already registered.';
$lang['err_already_logged_in']      = 'You are already logged in.';
$lang['err_invalid_credentials']    = 'Invalid credentials, please try again.';
$lang['err_email_not_changed']      = 'Failed to change the email address.';
$lang['err_missing_passwords']      = 'Please review the password fields.';
$lang['err_passwords_unmatched']    = 'The passwords are not matched, please review the fields.';
$lang['err_passwords_match']        = 'Password and Retype Password fields must be same.';
$lang['err_wrong_password']         = 'Invalid current password.';
$lang['err_invalid_email']          = 'Invalid email address.';
$lang['err_user_blocked']           = 'You have been blocked from this site.';
$lang['err_user_banned']            = 'Your account has been banned from this site.';
$lang['err_pass_reset_token_req']   = 'In the last 15 minutes, you have already requested a password reset.';
$lang['err_too_many_attempts']      = 'Too many invalid attempts, please kindly wait for %s before trying again.';
$lang['err_registration_disabled']  = 'Sorry, the user registration is temporarily disabled.';
$lang['err_reg_add_sess']           = 'The user is successfully registerd but, failed to login. Please try to login directly.';
$lang['err_send_pass_fe']           = 'The user is successfully registerd but, password sending is failed.';
$lang['err_ev_token_update_failed'] = 'Failed to set the email verification token.';
$lang['err_ic_expired']             = 'Invalid invitation code, the code is used or expired.';
$lang['err_ic_invalid']             = 'Invalid invitation code.';
$lang['err_invalid_invitation']     = 'Invalid request, no invitation is found for this code.';
$lang['err_other_provider']         = 'The user with this account\'s email address is already registered with another source.';
$lang['err_cant_impersonate']       = 'You cannot start impersonation with this as you already in.';
$lang['err_user_cant_delete']       = 'You cannot delete yourself.';
$lang['err_invalid_2fa_code']       = 'Invalid 2FA code, please try again.';
$lang['err_already_verified']       = 'This user is already verified.';
$lang['err_cant_delete_du']         = 'The default user cannot be deleted.';
$lang['err_cant_cut_zero']          = 'Unable to cut as the credit will be less than zero after cutting.';
$lang['err_cant_cut_currency']      = 'No credit is available in this currency for this user.';
$lang['err_email_taken']            = 'The email address is already taken.';
$lang['err_username_taken']         = 'The username is already taken.';
$lang['err_already_email_pending']  = 'The inputted email address is already pending.';
$lang['err_pwd_strong']             = 'The Password field must contain, number, letter, and special character with a minimum of 12 characters.';
$lang['err_pwd_medium']             = 'The Password field must contain, number, and lower and uppercase letter with a minimum of 8 characters.';
$lang['err_pwd_normal']             = 'The Password field must contain, number, and letter with a minimum of 6 characters.';
$lang['err_pwd_low']                = 'The Password field length must be at least 6 characters.';
$lang['err_same_password']          = 'Seems your password is the same as before, this must be different.';
$lang['err_u_change_not_allowed']   = 'Password, Role, and Status are not allowed to change for the default user.';

// Support:
$lang['err_ticket_closed']          = 'The reply cannot be added to the closed ticket.';
$lang['err_invalid_priority']       = 'Invalid priority value, please refresh your page and try again.';
$lang['err_invalid_category']       = 'Invalid category, please refresh your page and try again.';
$lang['err_ticket_fe']              = 'The reply is successfully added but, email notification sending is failed.';

// Subscribers ( Newsletter ):
$lang['err_invalid_conf_sub_nl']    = 'Invalid request, please make sure about the token and the newsletter confirmation status.';
$lang['err_invalid_nl_unsub_token'] = 'Invalid request, please make sure that your token is valid.';
$lang['err_already_nl_subscribed']  = 'This address is already added in the newsletter subscription list.';
$lang['err_failed_subscribe_nl']    = 'Failed to subscribe the newsletter, please try again later.';
$lang['err_captcha']                = 'Please make sure that you have successfully solved the captcha.';
$lang['err_no_confirmed_sub']       = 'No confirmed subscribers found.';

// Settings:
$lang['err_missing_mm_message']     = 'Please leave a maintenance mode message for the visitors.';
$lang['err_invalid_avatar_size']    = 'Invalid input passed for the maximum avatar size.';
$lang['err_invalid_ak']             = 'Invalid format passed for the access key field.';
$lang['err_role_exists']            = 'This role is already exists.';
$lang['err_permission_exists']      = 'This permission is already exists.';

// Tools:
$lang['err_cant_block']             = 'You cannot block your IP address.';
$lang['err_review_ip']              = 'Invalid IP(s) found, please review the IP addresses field.';
$lang['err_av_not_found']           = 'No activities log found for this request.';
$lang['err_missing_template']       = 'Email template for this hook or language is not found.';
$lang['err_et_exists']              = 'Email template for this hook and language is already exists.';
$lang['err_options_req']            = 'The Options field is required for the selected field type.';

// Payments:
$lang['err_unlimited_time']         = 'You have already purchased the item with unlimited time.';
$lang['err_item_not_for_sale']      = 'Sorry, this item is not available for sale.';
$lang['err_payment_failed']         = 'Transaction failed.';
$lang['err_insufficient_credit']    = 'Your balance is insufficient for this transaction.';
$lang['err_dont_have_currency']     = 'You don\'t have credit in the selected item price currency.';
