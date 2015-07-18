<?php namespace Phonex\Utils;
/**
 * Created by PhpStorm.
 * User: miroc
 * Date: 18.7.15
 * Time: 21:30
 */

use DateTime;

class DateRangeValidator
{
    /**
     * Validate that there are two dates, separated by color
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validate($attribute, $value, $parameters)
    {
        $dateFormat = $parameters[0];
        $dates = explode(':', $value);
        if (!$dates || count($dates) != 2){
            return false;
        }
        foreach($dates as $d){
            $date = trim($d);
            if (!$this->validateDate($date, $dateFormat)){
                return false;
            }
        }
        return true;
    }

    private function validateDate($date, $format)
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

}