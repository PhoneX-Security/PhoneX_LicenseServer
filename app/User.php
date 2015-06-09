<?php namespace Phonex;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Log;
use Phonex\Utils\SortableTrait;
use Queue;

/**
 * @property bool|mixed email
 * @property bool|mixed username
 * @property string password
 * @property int has_access
 * @property int confirmed
 * @property mixed subscriber_id
 * @property mixed subscriber
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract {
	use Authenticatable, CanResetPassword, SortableTrait;

    // legacy names
    const CREATED_AT = 'dateCreated';
    const UPDATED_AT = 'dateUpdated';

    protected $table = 'users';

	protected $fillable = ['username', 'email', 'password', 'has_access'];
	protected $sortable = ['username', 'email', 'has_access', 'id'];

	protected $hidden = ['password', 'remember_token'];

    /* relations */
	public function licenses(){
		return $this->hasMany('Phonex\License', 'user_id');
	}

    public function issuedLicenses(){
        return $this->hasMany('Phonex\License', 'issuer_id');
    }

    public function createdBusinessCodes(){
        return $this->hasMany('Phonex\BusinessCode', 'creator_id');
    }

    public function subscriber(){
        // weird, parameters 2 + 3 are switched ()
//        return $this->hasOne('Phonex\Subscriber', 'subscriber_id', );
        return $this->hasOne('Phonex\Subscriber', 'id', 'subscriber_id');
    }

    public function groups(){
        return $this->belongsToMany('Phonex\Group', 'user_group', 'user_id', 'group_id');
    }

    /* helper functions */
    public function addToContactList(User $user, $displayName = null){
        $subscriber1 = $this->subscriber;
        $subscriber2 = $user->subscriber;
        $dn = $displayName ? $displayName : $user->username;
        $subscriber1->addToContactList($subscriber2, $dn);
        Log::info('addToContactList; user has been added to contact list', [$this->username, $user->username, $dn]);
        try {
            Queue::push('ContactListUpdated', ['username'=>$user->email], 'users');
        } catch (\Exception $e){
            Log::error('cannot push ContactListUpdated message', [$e]);
        }
    }

    public function removeFromContactList(User $user){
        $subscriber1 = $this->subscriber;
        $subscriber2 = $user->subscriber;

        Log::info('removeFromContactList; user is being removed to contact list', [$this->username, $user->username]);

        $subscriber1->removeFromContactList($subscriber2);
        try {
            Queue::push('ContactListUpdated', ['username'=>$this->email], 'users');
        } catch (\Exception $e){
            Log::error('cannot push ContactListUpdated message', [$e]);
        }
    }

    public static function findByUsername($username){
        return User::where('username', $username)->first();
    }

    public static function getSupportUser(){
//        return User::where('username', "support")->first();
        return User::where('username', "phonex-support")->first();
    }

    public function deleteWithLicenses(){
        $licenses = $this->licenses;
        foreach($licenses as $license){
            $license->delete();
        }

        $subscriber = $this->subscriber;
        // delete cl
        if ($subscriber){
            ContactList::where('subscriber_id', $subscriber->id)
                ->orWhere('int_usr_id', $subscriber->id)
                ->delete();

            $subscriber->delete();
        }


        $this->groups()->detach();
        $this->delete();
    }
}
