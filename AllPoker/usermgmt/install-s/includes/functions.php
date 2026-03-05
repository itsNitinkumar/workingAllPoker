<?php

/**
 * Functions ( Install )
 *
 * @author Shahzaib
 */


/**
 * Send GET cURL Request
 *
 * @param   string $url
 * @return  mixed
 * @version 1.9
 */
function send_get_curl($url)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'cURL Error: ' . curl_error($ch);
        curl_close($ch);
        return null;
    }

    curl_close($ch);

    if (!empty($response)) {
        return json_decode($response);
    }

    return null;
}

/**
 * Error ( Direct Text )
 *
 * @param   string $text
 * @return  void
 * @version 1.9
 */
function d_error($text)
{
    $data = ['value' => $text, 'status' => 'false'];
    exit(json_encode($data));
}

/**
 * Base URL
 *
 * @param  string $path
 * @return string
 */
function base_url($path = '')
{
    $base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https://' : 'http://';
    $base_url .= $_SERVER['HTTP_HOST'];
    $base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

    if (! empty($path)) {
        $base_url .= $path;
    }

    return $base_url;
}

/**
 * URL Where the Script is Installed.
 *
 * @param   string $path
 * @return  string
 * @version 2.1
 */
function install_url($path)
{
    $url = base_url();
    $keyword = 'install';

    if (substr($url, -1) == '/') {
        $keyword = 'install/';
    }

    $url = str_replace($keyword, '', $url);

    return $url . $path;
}

/**
 * Status
 *
 * @param  string $status
 * @return string
 */
function status($status)
{
    if ($status === true) {
        return '<i class="z-fa fas fa-check-circle text-success mr-1"></i> Passed';
    }

    return '<i class="z-fa fas fa-times-circle text-danger mr-1"></i> Failed';
}

/**
 * Activate Step
 *
 * @param  string $value
 * @return string
 */
function activate_step($value)
{
    $class = '';
    $step = '';

    if (! empty($_GET['step'])) {
        $step = strtolower($_GET['step']);
    }

    if ($step === $value) {
        $class = 'active';
    }

    return $class;
}

/**
 * Do Clean
 *
 * @param  string  $string
 * @param  boolean $lower
 * @return string
 */
function do_clean($string, $lower = false)
{
    $string = trim($string);
    $string = stripslashes($string);

    if ($lower === true) {
        $string = strtolower($string);
    }

    $string = strip_tags($string);

    return $string;
}

/**
 * Post
 *
 * @param  string $key e.g. full_name
 * @return string
 */
function post($key)
{
    $string = '';

    if (! empty($_POST[$key])) {
        $string = $_POST[$key];
    }

    return $string;
}

/**
 * Is Mod Rewrite Enabled
 *
 * @return  boolean
 * @version 2.1
 */
function is_mod_rewrite_enabled()
{
    $status = false;

    if (function_exists('apache_get_modules')) {
        $status = in_array('mod_rewrite', apache_get_modules());
    }

    return $status;
}

/**
 * Requirements
 *
 * @param  string  $type
 * @param  boolean $icon
 * @return mixed
 */
function requirements($type, $icon = true)
{
    $status = false;

    switch ($type) {
        case 'php_version':
            $status = version_compare(PHP_VERSION, '7.2', '>=');
            break;

        case 'mysqli':
            $status = function_exists('mysqli_connect');
            break;

        case 'mod_rewrite':
            if (function_exists('apache_get_modules')) {
                $status = in_array('mod_rewrite', apache_get_modules());
            }

            break;

        case 'mbstring':
            $status = extension_loaded('mbstring');
            break;

        case 'curl':
            $status = extension_loaded('curl');
            break;

        case 'gd':
            $status = extension_loaded('gd');
            break;

        case 'zip';
            $status = extension_loaded('zip');
            break;
    }

    if ($icon) {
        return status($status);
    }

    return $status;
}

/**
 * Verify Requirements
 *
 * @return boolean
 */
function verify_requirements()
{
    $types = ['php_version', 'mysqli', 'mbstring', 'curl', 'gd', 'zip'];

    foreach ($types as $type) {
        $status = requirements($type, false);

        if ($status === false) return false;
    }

    return true;
}

/**
 * Permissions
 *
 * @param  string  $file
 * @param  boolean $icon
 * @return mixed
 */
function permission($file, $icon = true)
{
    $status = is_writable('../' . $file);

    if ($icon) {
        return status($status);
    }

    return $status;
}

/**
 * Verify Permissions
 *
 * @return boolean
 */
function verify_permissions()
{
    $files = ['application/config', 'uploads/images', 'assets/backups'];

    foreach ($files as $file) {
        $status = permission($file, false);

        if ($status === false) break;
    }

    return $status;
}

/**
 * Jump
 *
 * @param  string $step
 * @return void
 */
function jump($step)
{
    $target = base_url('?step=' . $step);
    $data = ['value' => $target, 'status' => 'true'];
    exit(json_encode($data));
}

/**
 * Error
 *
 * @param  string $key
 * @return void
 */
function error($key)
{
    global $errors;
    $data = ['value' => $errors[$key], 'status' => 'false'];
    exit(json_encode($data));
}
