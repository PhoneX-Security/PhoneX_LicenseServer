<?php namespace Phonex;

use Illuminate\Database\Eloquent\Model;
use Phonex\Model\Product;

class BusinessCodesExport extends Model{
	protected $table = 'business_codes_exports';
	protected $dates = ['expires_at'];

	public function creator()
	{
		return $this->belongsTo('Phonex\User', 'creator_id');
	}

	public function codes()
	{
		return $this->hasMany('Phonex\BusinessCode', 'export_id');
	}

	public function group(){
		return $this->belongsTo('Phonex\Group', 'group_id');
	}

	public function parent()
	{
		return $this->belongsTo('Phonex\User', 'parent_id');
	}

	public function product()
	{
		return $this->belongsTo(Product::class, 'product_id');
	}

	/**
	 * @deprecated
	 */
	public function licenseType()
	{
		return $this->belongsTo('Phonex\LicenseType', 'license_type_id');
	}

	/**
	 * @deprecated
	 */
	public function licenseFuncType()
	{
		return $this->belongsTo('Phonex\LicenseFuncType', 'license_func_type_id');
	}
}
