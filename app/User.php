<?php namespace Phonex;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Phonex\Utils\SortableTrait;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {
	use Authenticatable, CanResetPassword, SortableTrait;

//	const CREATED_AT = 'dateCreated';
//	const UPDATED_AT = 'dateUpdated';

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['username', 'email', 'password', 'has_access'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];

//	public function licenses(){
//		return $this->hasMany('PhonexLicense', 'user_id');
//	}

}
