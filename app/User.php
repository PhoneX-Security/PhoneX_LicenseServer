<?php namespace Phonex;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Phonex\Utils\SortableTrait;
use Phonex\Utils\InputGet;


class User extends Model implements AuthenticatableContract, CanResetPasswordContract {
	use Authenticatable, CanResetPassword, SortableTrait;

	protected $table = 'users';

	protected $fillable = ['username', 'email', 'password', 'has_access'];
	protected $sortable = ['username', 'email', 'has_access', 'id'];

	protected $hidden = ['password', 'remember_token'];

	public function licenses(){
		return $this->hasMany('Phonex\User', 'user_id');
	}
}
