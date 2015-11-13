<?php namespace Phonex\Model;

use Illuminate\Database\Eloquent\Model;

class ProductTranslation extends Model{
    protected $table = 'products_translations';

    public $timestamps = false;
    protected $fillable = ['display_name', 'description'];
}
