<?php
use Carbon\Carbon;

/**
 * @param $type
 * @param $name
 * @param $filter
 * @param bool $default
 * @return bool|mixed Value of the requested variable on success, FALSE if the filter fails, or NULL if the $name variable is not set.
 */
function getParameter($type, $name, $filter, $default = FALSE)
{
    $val = filter_input($type, $name, $filter);
    if ($default === null){
        return $val;
    }

    if ($val === null || $val == false){
        return $default;
    } else {
        return $val;
    }
}

/**
 * Additional URL helper
 * @param $col
 * @param null $title
 * @return string
 */
function link_to_sort($col, $title = null){
    if (is_null($title)) {
        $title = str_replace('_', ' ', $col);
        $title = ucfirst($title);
    }

    $arrowDown = '<span class="caret"></span>';
    $arrowUp = '<span class="caret caret-reversed"></span>';

    $indicator = (\Request::get('s') == $col ? (\Request::get('o') === 'asc' ? $arrowUp : $arrowDown) : "");
    $parameters = array_merge(Input::except(['page']), array('s' => $col, 'o' => (\Request::get('o') === 'asc' ? 'desc' : 'asc')));
    return link_to_route(Route::currentRouteName(), $title, $parameters) . $indicator;
}

if (!function_exists('date_simple')){
    function date_simple($datetime){
        if (!$datetime){
            return '';
        } else {
            return Carbon::parse($datetime)->format('Y-m-d.');
        }
    }
}

/**
 * OpenSips specific functions for password generation
 */
if (!function_exists('getHA1_1')) {
    function getHA1_1($sip, $password)
    {
        // split sip by @
        $arr = explode("@", $sip, 2);
        if ($arr == null || count($arr) != 2) {
            var_dump($arr);
            throw new Exception("Invalid SIP format");
        }
        return getHA1_2($arr[0], $arr[1], $password);
    }
}

if (!function_exists('getHA1_B')) {
    function getHA1_B($sip, $password)
    {
        // split sip by @
        $arr = explode("@", $sip, 2);
        if ($arr == null || count($arr) != 2) {
            var_dump($arr);
            throw new Exception("Invalid SIP format");
        }
        return getHA1_2($sip, $arr[1], $password);
    }
}

if (!function_exists('getHA1_2')) {
    function getHA1_2($username, $domain, $password)
    {
        $x = $username . ":" . $domain . ":" . $password;
        return md5($x);
    }
}

if (!function_exists('dbDatetime')){
    /**
     *
     * @param timestamp the optional timestamp parameter is an integer Unix timestamp, default value is time()
     * @param type $format, default is datetime for mysql
     * @return string ready for DB
     */
    function dbDatetime($time = null, $format = "Y-m-d H:i:s"){
        if ($time === null){
            $time = time();
        }
        return date($format, $time);
    }
}

if (!function_exists('getRandomString')) {
    function getRandomString($len = 80, $characters = "abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ123456789_.-"){
        $characters = str_shuffle($characters);
        $cons = '';

        for ($i = 0; $i < $len; $i++){
            $cons .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $cons;
    }
}

if (!function_exists('bcodeDashes')) {
    function bcodeDashes($code){
        return substr($code, 0, 3) . "-" . substr($code, 3, 3) . "-" . substr($code, 6);
    }
}

/* Most commonly used function for getting Carbon object from input */
if (!function_exists('carbonFromInput')){
    function carbonFromInput($input, $format = "d-m-Y"){
        return Carbon::createFromFormat($format, $input);
    }
}
