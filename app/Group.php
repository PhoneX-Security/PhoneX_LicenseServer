<?php namespace Phonex;

use Illuminate\Database\Eloquent\Model;

class Group extends Model{
	protected $table = 'groups';
    protected $fillable = ['name', 'owner_id'];

    public function users()
    {
        return $this->belongsToMany('Phonex\User', 'user_group', 'group_id', 'user_id');
    }

    public function owner()
    {
        return $this->belongsTo('Phonex\User', 'owner_id');
    }

    public function bcodes()
    {
        return $this->hasMany('Phonex\BusinessCode', 'group_id');
    }

    public function exports()
    {
        return $this->hasMany('Phonex\BusinessCode', 'group_id');
    }

    /* Helpers */
    public static function findByName($groupName)
    {
        return Group::where('name', $groupName)->first();
    }
}
