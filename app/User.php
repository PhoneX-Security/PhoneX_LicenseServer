<?php namespace Phonex;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Phonex\Utils\SortableTrait;

/**
 * @property bool|mixed email
 * @property bool|mixed username
 * @property string password
 * @property int has_access
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract {
	use Authenticatable, CanResetPassword, SortableTrait;

    // legacy names
    const CREATED_AT = 'dateCreated';
    const UPDATED_AT = 'dateUpdated';


    protected $table = 'phonex_users';

	protected $fillable = ['username', 'email', 'password', 'has_access'];
	protected $sortable = ['username', 'email', 'has_access', 'id'];

	protected $hidden = ['password', 'remember_token'];

	public function licenses(){
		return $this->hasMany('Phonex\License', 'user_id');
	}

    public function issuedLicenses(){
        return $this->hasMany('Phonex\License', 'issuer_id');
    }
}
