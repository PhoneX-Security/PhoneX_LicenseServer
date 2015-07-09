<?php namespace Phonex;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed id
 * @property mixed name
 */
class LicenseFuncType extends Model{
    const TYPE_FULL = "full";
    const TYPE_TRIAL = "trial";
    const TYPE_COMPANY_CLIENT = "company_client";
    const TYPE_CALL_ME_CLIENT = "call_me_client";
    const TYPE_POOL_CLIENT = "pool_client";

    protected $table = "license_func_types";
    protected $fillable = ['name'];

    public static function getFull(){
        return self::getByType(self::TYPE_FULL);
    }

    public static function getTrial(){
        return self::getByType(self::TYPE_TRIAL);
    }

    public static function getCompanyClient(){
        return self::getByType(self::TYPE_COMPANY_CLIENT);
    }

    public static function getCallMeClient(){
        return self::getByType(self::TYPE_CALL_ME_CLIENT);
    }

    public static function getPoolClient(){
        return self::getByType(self::TYPE_POOL_CLIENT);
    }

    public static function getByType($funcType){
        return LicenseFuncType::where('name', $funcType)->first();
    }

    /* Accessors */
    public function getUcNameAttribute()
    {
        return ucfirst($this->name);
    }

}
