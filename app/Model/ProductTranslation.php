<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTranslation extends Model{
    protected $table = 'products_translations';
    protected $connection = 'mysql_lic';

    public $timestamps = false;
    protected $fillable = ['display_name'];
}
