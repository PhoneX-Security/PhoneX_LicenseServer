<?php namespace Phonex\Utils;
/**
 * Created by PhpStorm.
 * User: miroc
 * Date: 27.1.15
 * Time: 19:40
 */
use Carbon\Carbon;


/**
 * Class Get
 * Using late static binding here: http://php.net/manual/en/language.oop5.late-static-bindings.php
 * example: static::$type instead of self::$type - to be evaluated on runtime
 */

class InputGet{
    protected static $type = INPUT_GET;

    public static function has($name){
        return filter_has_var(static::$type, $name);
    }

    public static function hasNonEmpty($name){
        $var = static::get($name);
        return filter_has_var(static::$type, $name) && !empty($var);
    }

    // All methods: Returns value of the requested variable on success, FALSE if the validation filter fails, or NULL if the $name variable is not set.
    // TODO throw Exception when filter fails
    public static function get($name, $default = false){
        return getParameter(static::$type, $name, FILTER_UNSAFE_RAW, $default);
    }

    // TODO implement
    public static function getAlphaNum($name, $default = false){
        return getParameter(static::$type, $name, FILTER_UNSAFE_RAW, $default);
    }

    public static function getInteger($name, $default = false){
        return getParameter(static::$type, $name, FILTER_VALIDATE_INT, $default);
    }

    public static function getEmail($name, $default = false){
        return getParameter(static::$type, $name, FILTER_VALIDATE_EMAIL, $default);
    }

    /**
     * @param $name
     * @param bool $default
     * @return static Carbon
     */
    public static function getCarbonTime($name, $default = false){
        $rawDate = static::get($name, $default);
        return Carbon::parse($rawDate);
    }
}

