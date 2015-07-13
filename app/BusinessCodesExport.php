<?php namespace Phonex;

use Illuminate\Database\Eloquent\Model;

class BusinessCodesExport extends Model{
	protected $table = 'business_codes_exports';

	public function creator()
	{
		return $this->belongsTo('Phonex\User', 'creator_id');
	}

	public function codes()
	{
		return $this->hasMany('Phonex\BusinessCode', 'export_id');
	}
}
