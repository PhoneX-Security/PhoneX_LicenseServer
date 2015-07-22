<?php namespace Phonex;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed days
 * @property mixed id
 */
class LicenseType extends Model{
    const EXPIRATION_DAY = "day";
    const EXPIRATION_WEEK = "week";
    const EXPIRATION_MONTH = "month";
    const EXPIRATION_QUARTER = "quarter";
    const EXPIRATION_HALF_YEAR = "half_year";
    const EXPIRATION_YEAR = "year";
    const EXPIRATION_INFINITE = "infinite";

    protected $table = "license_types";
    
    public function licenses(){
        return $this->hasMany('Phonex\License', 'license_type_id');
    }

    /* Helpers */
    public function readableType(){
        return ucfirst($this->name) . " (" .  $this->days . " days )";
    }

    public static function getWeek(){
        return self::findByName(self::EXPIRATION_WEEK);
    }
    public static function getDay(){
        return self::findByName(self::EXPIRATION_DAY);
    }
    public static function getMonth(){
        return self::findByName(self::EXPIRATION_MONTH);
    }
    public static function getQuarterOfYear(){
        return self::findByName(self::EXPIRATION_QUARTER);
    }
    public static function getHalfYear(){
        return self::findByName(self::EXPIRATION_HALF_YEAR);
    }
    public static function getYear(){
        return self::findByName(self::EXPIRATION_YEAR);
    }
    public static function getInfinite(){
        return self::findByName(self::EXPIRATION_INFINITE);
    }

    public static function findByName($name){
        return LicenseType::where('name', $name)->first();
    }

    /* Accessors */
    //uc_name_with_days
    public function getUcNameWithDaysAttribute()
    {
        return ucfirst($this->name) . " (" . $this->days . " days)";
    }

    public function getUcNameAttribute()
    {
        return ucfirst($this->name);
    }


}
