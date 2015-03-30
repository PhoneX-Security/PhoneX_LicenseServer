<?php namespace Phonex;

use Illuminate\Database\Eloquent\Model;

class BusinessCode extends Model{
	protected $table = 'business_codes';

    public function group(){
        return $this->belongsTo('Phonex\Group', 'group_id');
    }

    public function licenseType(){
        return $this->belongsTo('Phonex\LicenseType', 'license_type_id');
    }

    public function licenses(){
        return $this->hasMany('Phonex\License', 'business_code_id');
    }
}
