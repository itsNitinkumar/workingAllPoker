<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller']          = 'home';
$route['confirm_subscription/(:any)'] = 'home/confirm_subscription/$1';
$route['unsubscribe/(:any)']          = 'home/unsubscribe/$1';
$route['login']                       = 'account/login';
$route['login/banned']                = 'account/login/banned';
$route['login/facebook']              = 'account/login_facebook';
$route['login/twitter']               = 'account/login_twitter';
$route['login/google']                = 'account/login_google';
$route['login/vkontakte']             = 'account/login_vkontakte';
$route['2f_authentication']           = 'account/two_factor_authentication';
$route['logout']                      = 'account/logout';
$route['everify/(:num)/(:any)']       = 'account/everify/$1/$2';
$route['change_email/(:any)']         = 'account/change_email/$1';
$route['register']                    = 'account/register';
$route['register/invitation/(:any)']  = 'account/register/$1';
$route['forgot_password']             = 'account/forgot_password';
$route['change_password/(:any)']      = 'account/change_password/$1';
$route['dashboard']                   = 'user/dashboard';
$route['user/sessions/(:num)']        = 'user/tools/sessions/$1';
$route['user/sessions']               = 'user/tools/sessions';
$route['user/activities_log/(:num)']  = 'user/tools/activities_log/$1';
$route['user/activities_log']         = 'user/tools/activities_log';
$route['terms']                       = 'home/page/1';
$route['privacy-policy']              = 'home/page/2';

// @version 1.5:
$route['page/(:any)']                 = 'home/custom_page/$1';

$route['404_override']                = '';
$route['translate_uri_dashes']        = FALSE;
