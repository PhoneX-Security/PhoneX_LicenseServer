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
    public function validateDateRange($attribute, $value, $parameters)
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

    /**
     * Parse Carbon dates from dateRange value
     * @param $value
     * @param string $format
     * @return array [$dateFrom, $dateTo]
     */
    public static function retrieveDates($value, $format = 'Y-m-d')
    {
        return array_map(function($item) use ($format){
            return carbonFromInput(trim($item), $format);
        }, explode(":", $value) );
    }

    private function validateDate($date, $format)
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

}