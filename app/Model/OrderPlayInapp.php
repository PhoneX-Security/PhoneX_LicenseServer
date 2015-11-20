<?php namespace Phonex\Model;

use Illuminate\Database\Eloquent\Model;
use Phonex\User;

class OrderPlayInapp extends Model{
	protected $table = 'orders_play_inapp';
	protected $dates = ['purchase_time'];

	// these columns can be updated via model->update and similar methods
	protected $fillable = ['license_id'];

	public $timestamps = false;

	/* Helper functions*/
	public static function orderIdExists($playOrderId)
	{
		return OrderPlayInapp::where('play_order_id', $playOrderId)
//			->where('user_id', $user->id)
//			->whereNotNull('license_id')
			->count() > 0;
	}

}
