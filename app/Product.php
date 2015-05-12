<?php namespace Phonex;

use Illuminate\Database\Eloquent\Model;

class Product extends Model{
	protected $table = 'products';
    protected $visible = ['name', 'description', 'id', 'productPrices'];

    public function productPrices(){
        return $this->hasMany('Phonex\ProductPrice');
    }
}
