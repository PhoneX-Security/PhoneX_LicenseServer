<?php namespace Phonex;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string code
 * @property  group_id
 * @property  license_type_id
 * @property int licenses_limit
 * @property int is_active
 * @property mixed id
 */
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

    /**
     * Contact list mappings
     * Makes sense when a single license is allowed per given business code
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function clMappings(){
        return $this->belongsToMany('Phonex\BusinessCode', 'business_code_cl_mappings', 'cl_owner_bcode_id', 'contact_bcode_id');
    }

    public static function generateUniqueCode(){
        $chars = 'abcdefghjkmnpqrstuvwxyz0123456789';
        $code = getRandomString(8, $chars);
        while (BusinessCode::where('code', $code)->count() > 0){
            $code = getRandomString(8, $chars);
        }
        return $code;
    }
}
