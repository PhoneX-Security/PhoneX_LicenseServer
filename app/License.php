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

	protected $table = 'phonex_licenses';

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

    public function isActive(){
        if (!$this->expires_at || Carbon::now()->gt(Carbon::parse($this->expires_at))) {
            return false;
        } else {
            return true;
        }
    }
}
