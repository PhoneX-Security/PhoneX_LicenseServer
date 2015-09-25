<?php namespace Phonex\Model;

use Illuminate\Database\Eloquent\Model;

class Product extends Model{
	protected $table = 'products';
    protected $visible = ['name', 'description', 'id', 'productPrices', 'platform', 'priority', 'appPermissions'];

    public function appPermissions(){
        return $this->belongsToMany(AppPermission::class, 'product_app_permission', 'product_id', 'app_permission_id')->withPivot(['count']);
    }

    public function productPrices(){
        return $this->hasMany(ProductPrice::class);
    }
}
