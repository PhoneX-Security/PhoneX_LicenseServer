<?php namespace Phonex\Model;

use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model{
	protected $table = 'product_prices';
    protected $visible = ['currency', 'cost'];
}
