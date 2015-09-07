<?php namespace Phonex;

use BeatSwitch\Lock\Callers\Caller;
use BeatSwitch\Lock\LockAware;
use Carbon\Carbon;
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
class User extends Model implements AuthenticatableContract, CanResetPasswordContract, Caller {
	use Authenticatable, CanResetPassword, SortableTrait, LockAware;

    // legacy names
    const CREATED_AT = 'dateCreated';
    const UPDATED_AT = 'dateUpdated';

    protected $table = 'users';

	protected $fillable = ['username', 'email', 'password', 'has_access'];
	protected $sortable = ['username', 'email', 'has_access', 'id'];
	protected $hidden = ['password', 'remember_token'];

    /* Relations */
	public function licenses(){
		return $this->hasMany('Phonex\License', 'user_id');
	}

    /**
     * Auxiliary column 'active_license_id' is computed periodically (see RefreshSubscriber) and points to current active license
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function activeLicense()
    {
        return $this->hasOne('Phonex\License', 'id', 'active_license_id');
    }

    public function issuedLicenses()
    {
        return $this->hasMany('Phonex\License', 'issuer_id');
    }

    public function createdBusinessCodes()
    {
        return $this->hasMany('Phonex\BusinessCode', 'creator_id');
    }

    public function subscriber()
    {
        // weird, parameters 2 + 3 are switched ()
        return $this->hasOne('Phonex\Subscriber', 'id', 'subscriber_id');
    }

    public function groups()
    {
        return $this->belongsToMany('Phonex\Group', 'user_group', 'user_id', 'group_id');
    }

    public function roles()
    {
        return $this->belongsToMany('Phonex\Role', 'user_role', 'user_id', 'role_id');
    }

    /* Scopes */

    /* Some magic here, for more info, see Laravel Eloquent Scopes */
//    public function scopeOfGroups($query, array $groupIds)
//    {
//        return $query->select('users.*')
//            ->join('user_group', 'users.id', '=', 'user_group.user_id')
//            ->groupBy('users.id')
//            ->whereIn('group_id', $groupIds);
//    }

    /* Accessors */
    // roles_list
    public function getRolesListAttribute(){
        return implode(", ", $this->roles->fetch('display_name')->toArray());
    }
    // successful_trial_request
    public function getSuccessfulTrialRequestAttribute()
    {
        return TrialRequest::where(['username' => $this->username, 'isApproved'=>1])->first();
    }

    /* Helper functions */
    public function addToContactList(User $user, $displayName = null){
        $subscriber1 = $this->subscriber;
        $subscriber2 = $user->subscriber;
        $dn = $displayName ? $displayName : $user->username;
        $subscriber1->addToContactList($subscriber2, $dn);
        Log::info('addToContactList; user has been added to contact list', [$this->username, $user->username, $dn]);
        try {
            Queue::push('ContactListUpdated', ['username'=>$this->email], 'users');
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

    /**
     * Returns small user object only containing user's id and username (used in text reports)
     */
    public function getUserObj()
    {
        $obj = new \stdClass();
        $obj->id = $this->id;
        $obj->username = $this->username;
        return $obj;
    }

    public static function findByUsername($username){
        return User::where('username', $username)->first();
    }

    public static function findByEmail($email){
        return User::where('email', $email)->first();
    }

    public static function getSupportUser(){
        return User::where('username', "phonex-support")->first();
    }

    public function deleteWithLicenses(){
        // first invalid active license pointer
        $this->active_license_id = null;
        $this->save();

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

    /* ACL */
    /**
     * The type of caller
     *
     * @return string
     */
    public function getCallerType()
    {
        // the same as in lock.php configuration
        return 'users';
    }

    /**
     * The unique ID to identify the caller with
     *
     * @return int
     */
    public function getCallerId()
    {
        return $this->id;
    }

    /**
     * The caller's roles
     *
     * @return array
     */
    public function getCallerRoles()
    {
        $dbRoles = $this->roles->fetch('name')->toArray();
        $defaultRoles = ['user'];
        return array_merge($defaultRoles, $dbRoles);
    }
}
