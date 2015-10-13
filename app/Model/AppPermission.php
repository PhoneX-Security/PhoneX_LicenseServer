<?php namespace Phonex\Model;

use Illuminate\Database\Eloquent\Model;

class AppPermission extends Model{
	protected $table = 'app_permissions';
    protected $visible = ['permission'];
    protected $casts = ['id' => 'integer'];

    // we manually want to include 'value' attribute in array/json if this model is retrieved using pivot table from product
    public function toArray()
    {
        $array = parent::toArray();
        if ($this->pivot && $this->pivot->value){
            $array['value'] = intval($this->pivot->value);
        }
        return $array;
    }
}
