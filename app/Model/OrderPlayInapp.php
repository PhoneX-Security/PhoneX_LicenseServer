<?php namespace Phonex\Model;

use Illuminate\Database\Eloquent\Model;

class OrderPlayInapp extends Model{
	protected $table = 'orders_play_inapp';
	protected $dates = ['purchase_time'];

	// these columns can be updated via model->update and similar methods
	protected $fillable = ['license_id'];

	public $timestamps = false;
}
