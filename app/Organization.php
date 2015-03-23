<?php namespace Phonex;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model{
	protected $table = 'organizations';

    public function users(){
        return $this->belongsToMany('Phonex\User', 'user_organization', 'organization_id', 'user_id');
    }
}
