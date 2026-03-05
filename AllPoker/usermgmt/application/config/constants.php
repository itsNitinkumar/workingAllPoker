<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code


// ZUM:
define( 'ADMIN_ACTIONS', 'actions/admin/' );
define( 'USER_ACTIONS', 'actions/user/' );
define( 'USER_PUBLIC_PAGES', ['login', '2f_authentication', 'register', 'forgot_password', 'change_password'] );
define( 'ALLOWED_IMG_EXT', 'gif|jpg|jpeg|png|bmp' );
define( 'ALLOWED_IMG_EXT_HTML', 'image/gif,image/jpg,image/jpeg,image/png,image/bmp' );
define( 'MAX_ALLOWED_IMG_SIZE', 5120 ); // In Kilobytes e.g. 5 MB = 5 * 1024
define( 'DEFAULT_DB_COLUMN', 'id' );
define( 'TXN_ID_PREFIX', 'txn_' );
define( 'ZUM_VERSION', '2.6' );
define( 'PER_PAGE_RESULTS_PANEL', 20 );

// @version 1.3
define( 'STATIC_DATE_FORMAT', 'Y-m-d' );

// @version 1.5
define( 'DB_BACKUP_TABLES', ['users'] ); // Specific database tables to backup

// @version 1.8
define( 'ZERO_DECIMAL_ISO', ['bif', 'clp', 'djf', 'gnf', 'jpy', 'kmf', 'krw', 'mga', 'pyg', 'rwf', 'ugx', 'vnd', 'vuv', 'xaf', 'xof', 'xpf'] );

// Cookies:
define( 'LANG_COOKIE', 'z_site_language' );
define( 'PREFIX_2FA', 'z_2fa_' );
define( 'USER_TOKEN', 'z_user_token' );

// @version 1.8
define( 'SIDEBAR_COOKIE', 'z_sidebar_collapsed' );


define( 'MODALS_SUPPORTED_PARENT_AREAS', ['admin', 'user'] );
define( 'NO_PERMISSION_MSG', '403 - You do not have permission of this area.' );
define( 'DEFAULT_USER_IMG', 'default.png' );

/**
 * Slugs to handle the parent and child menus of sidebar. Supports only parent and child.
 * Used to open and activate the parent dropdown menus.
 *
 * Related functions are declared in "helpers/z_pages_helper.php".
 */
define( 'PANEL_SLUGS', [
    'user' => [
        'payment' => ['items', 'log']
    ],
    'admin' => [
        'payment'  => ['items', 'log'],
        'support'  => ['categories', 'contact_messages', 'tickets', 'ticket'],
        'tools'    => ['announcements', 'custom_fields', 'email_templates', 'ip_manager', 'activities_log', 'backup', 'sessions'],
        'users'    => ['new_user', 'invites', 'manage', 'edit_user', 'adjust_balance', 'sessions', 'activities_log', 'sent_emails'],
        'settings' => ['general', 'support', 'users', 'roles', 'permissions', 'payment', 'apis', 'email']
    ]
]);

/**
 * Use this if you want to add the new language(s). Once a language will be added, that will
 * be shown in the available languages list ( e.g. language switcher ). You will be required
 * to add the language related translations in the "language/*" directory.
 *
 * NOTE: Once you add a new language, don't forget to add the related email templates.
 */
define( 'AVAILABLE_LANGUAGES',
[
    'english' => ['display_label' => 'English']
]);

define( 'SITE_THEMES',
[
    'default' => ['display_label' => 'Default']
]);

define( 'BACKUPS_DIRECTORY', 'assets/backups/' );
define( 'IMG_UPLOADS_DIR', 'uploads/images/' );
define( 'ADMIN_LTE_ASSETS', 'assets/%s/panel/admin_lte/' );
define( 'ASSETS_PATH', 'assets/%s/' );
