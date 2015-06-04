<?php namespace Phonex;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed days
 * @property mixed id
 */
class LicenseType extends Model{
    protected $table = "license_types";
    
    public function licenses(){
        return $this->hasMany('Phonex\License', 'license_type_id');
    }

    public function readableType(){
        return ucfirst($this->name) . " (" .  $this->days . " days )";
    }

    public static function findByName($name){
        return LicenseType::where('name', $name)->first();
    }
}
