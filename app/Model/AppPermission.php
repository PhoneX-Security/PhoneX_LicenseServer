<?php namespace Phonex\Model;

use Illuminate\Database\Eloquent\Model;

class AppPermission extends Model{
	protected $table = 'app_permissions';
    protected $visible = ['permission'];

    // we manually want to include 'count' attribute in array/json if this model is retrieved using pivot table from product
    public function toArray()
    {
        $array = parent::toArray();
        if ($this->pivot && $this->pivot->count){
            $array['count'] = $this->pivot->count;
        }
        return $array;
    }
}
