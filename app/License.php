<?php namespace Phonex;

use Illuminate\Database\Eloquent\Model;
use Phonex\Utils\SortableTrait;


class License extends Model{
//	use SortableTrait;

	protected $table = 'licenses';

//	protected $fillable = ['comment', 'starts_at', 'expires_at', 'has_access'];
//	protected $sortable = ['username', 'email', 'has_access', 'id'];

	public function user(){
		return $this->belongsTo('Phonex\User', 'user_id');
	}

	public function licenseType() {
		return $this->belongsTo('PhonexLicenseType', 'license_type_id');
	}
}
