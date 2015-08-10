<?php namespace Phonex;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
//use Phonex\Utils\SortableTrait;


/**
 * @property mixed user_id
 * @property mixed license_type_id
 * @property  issuer_id
 * @property bool|mixed comment
 * @property  issuer_id
 * @property mixed license_func_type_id
 */
class License extends Model{
//	use SortableTrait;

	protected $table = 'licenses';

	protected $fillable = ['comment'];
    protected $dates = ['starts_at', 'expires_at'];
//	protected $sortable = ['username', 'email', 'has_access', 'id'];

	public function user(){
		return $this->belongsTo('Phonex\User', 'user_id');
	}

    public function issuer(){
        return $this->belongsTo('Phonex\User', 'issuer_id');
    }

	public function licenseType() {
		return $this->belongsTo('Phonex\LicenseType', 'license_type_id');
	}

    public function licenseFuncType() {
        return $this->belongsTo('Phonex\LicenseFuncType', 'license_func_type_id');
    }

    public function businessCode() {
        return $this->belongsTo('Phonex\BusinessCode', 'business_code_id');
    }

    /* Accessors */
    // readable_type
    public function getReadableTypeAttribute()
    {
        return $this->licenseFuncType->uc_name . ' / ' . $this->licenseType->uc_name_with_days;
    }

    public function isActive()
    {
        if (!$this->expires_at || Carbon::now()->gt(Carbon::parse($this->expires_at))) {
            return false;
        } else {
            return true;
        }
    }

    public static function endingLater(License $lic1, License $lic2){
        return $lic2->expires_at->gte($lic1->expires_at) ? $lic2 : $lic1;
    }
    public static function startingEarlier(License $lic1, License $lic2){
        return $lic2->starts_at->lte($lic1->starts_at) ? $lic2 : $lic1;
    }
}
