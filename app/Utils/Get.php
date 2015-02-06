<?php namespace Phonex\Utils;
/**
 * Created by PhpStorm.
 * User: miroc
 * Date: 27.1.15
 * Time: 19:40
 */


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

    // All methods: Returns value of the requested variable on success, FALSE if the validation filter fails, or NULL if the $name variable is not set.
    // TODO throw Exception when filter fails
    public static function get($name, $default = false){
        return getParameter(static::$type, $name, FILTER_UNSAFE_RAW, $default);
    }

    public static function getInteger($name, $default = false){
        return getParameter(static::$type, $name, FILTER_VALIDATE_INT, $default);
    }

    public static function getEmail($name, $default = false){
        return getParameter(static::$type, $name, FILTER_VALIDATE_EMAIL, $default);
    }
}

class InputPost extends InputGet {
    protected static $type = INPUT_POST;
}