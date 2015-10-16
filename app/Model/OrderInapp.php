<?php namespace Phonex\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Phonex\LicenseFuncType;
use Phonex\LicenseType;

class OrderInapp extends Model{
	protected $table = 'orders_inapp';
	protected $dates = ['purchase_date'];

	// these columns can be updated via model->update and similar methods
	protected $fillable = ['state', 'license_id'];

	public $timestamps = false;
}
