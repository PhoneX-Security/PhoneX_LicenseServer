<?php namespace Phonex;

use Illuminate\Database\Eloquent\Model;

class Group extends Model{
	protected $table = 'groups';

    public function users(){
        return $this->belongsToMany('Phonex\User', 'user_group', 'group_id', 'user_id');
    }
}
