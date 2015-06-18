<?php namespace Phonex;

use Illuminate\Database\Eloquent\Model;

class Role extends Model{
	protected $table = 'roles';

    public function users(){
        return $this->belongsToMany('Phonex\User', 'user_role', 'role_id', 'user_id');
    }
}
