<?php namespace Phonex;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string code
 * @property  group_id
 * @property  license_type_id
 * @property int is_active
 * @property mixed id
 * @property int users_limit
 */
class BusinessCode extends Model{

    protected $table = 'business_codes';
    protected $dates = ['expires_at'];

    // WARNING - use get<GROUP|CREATOR|PARENT> instead of these functions
    public function group(){
        return $this->belongsTo('Phonex\Group', 'group_id');
    }

    public function creator(){
        return $this->belongsTo('Phonex\User', 'creator_id');
    }

    public function parent(){
        return $this->belongsTo('Phonex\User', 'parent_id');
    }

    public function licenseType(){
        return $this->belongsTo('Phonex\LicenseType', 'license_type_id');
    }

    public function licenseFuncType(){
        return $this->belongsTo('Phonex\LicenseFuncType', 'license_func_type_id');
    }

    public function users(){
        return $this->hasMany('Phonex\User', 'business_code_id');
    }

    public function licenses(){
        return $this->hasMany('Phonex\License', 'business_code_id');
    }

    public function export(){
        return $this->belongsTo('Phonex\BusinessCodesExport', 'export_id');
    }

    /* Accessors */
    // printable_code
    public function getPrintableCodeAttribute(){
        return bcodeDashes($this->code);
    }

    // number_of_usages
    public function getNumberOfUsagesAttribute()
    {
        return $this->licenses->count();
//        $usersIds = $this->users->pluck('id')->toArray();
//        foreach($this->licenses as $lic){
//            $id = $lic->user->id;
//            if (!in_array($id, $usersIds)){
//                $usersIds[] = $id;
//            }
//        }
//        return count($usersIds);
    }


    /**
     * Contact list mappings
     * Makes sense when a single license is allowed per given business code
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function clMappings(){
        return $this->belongsToMany('Phonex\BusinessCode', 'business_code_cl_mappings', 'cl_owner_bcode_id', 'contact_bcode_id');
    }

    /* Helpers */
    /* Properties of bcode can be set directly in business_code table (bigger priority), or parent export table */
    public function getLicenseType(){
        return $this->getAttributeFromThisOrExport("licenseType");
    }
    public function getLicenseFuncType(){
        return $this->getAttributeFromThisOrExport("licenseFuncType");
    }
    public function getGroup(){
        return $this->getAttributeFromThisOrExport("group");
    }
    public function getParent(){
        return $this->getAttributeFromThisOrExport("parent");
    }
    public function getExpiresAt(){
        return $this->getAttributeFromThisOrExport("expires_at");
    }

    private function getAttributeFromThisOrExport($attribute)
    {
        if ($this->$attribute){
            return $this->$attribute;
        } else if ($this->export && $this->export->$attribute){
            return $this->export->$attribute;
        } else {
            return null;
        }
    }

    public function getLicenseLimit()
    {
        if ($this->users_limit){
            return $this->users_limit;
        } else if ($this->export && $this->export->license_limit_per_code){
            return $this->export->license_limit_per_code;
        } else {
            return null;
        }
    }
}
