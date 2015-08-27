<?php namespace Phonex\Jobs;

use Illuminate\Contracts\Bus\SelfHandling;
use Log;
use Phonex\Events\AuditEvent;
use Phonex\Model\SupportNotification;
use Phonex\User;

class CreateUser extends Command implements SelfHandling {
    /**
     * @var
     */
    private $username;
    /**
     * @var
     */
    private $password;
    /**
     * @var array
     */
    private $groupsId;
    /**
     * @var bool
     */
    private $isQaTrial;
    /**
     * @var null
     */
    private $trialNumber;

    private $subscriberId;

    private $roleIds;

    private $comment;


    public function __construct($username, $groupsId = array(), $isQaTrial = false, $trialNumber = null){
        $this->username = $username;
        $this->groupsId = $groupsId;
        $this->isQaTrial = $isQaTrial;
        $this->trialNumber = $trialNumber;
    }

    public function addRoles($roleIds){
        $this->roleIds = $roleIds;
    }

    public function addAccess($password){
        $this->password = $password;
        return $this;
    }

    public function addComment($comment)
    {
        $this->comment = $comment;
    }

    // for legacy users already existing in subscriber table
    public function setSubscriberId($subscriberId){
        $this->subscriberId = $subscriberId;
    }

	public function handle(){
        $user = new User();
        $user->username = $this->username;
        $user->email = $user->username . "@phone-x.net";
        $user->trialNumber = $this->trialNumber;
        $user->confirmed = 1;
        $user->qa_trial = $this->isQaTrial ? 1 : 0;
        $user->has_access = 0;

        if ($this->password){
            // Do not issue has_access automatically
//            $user->has_access = 0;
            $user->password = bcrypt($this->password);
        }
        if ($this->subscriberId){
            $user->subscriber_id = $this->subscriberId;
        }
        if ($this->comment){
            $user->comment = $this->comment;
        }


        $saved = $user->save();
        event(AuditEvent::create('user', $user->id));

        // allow user to try again
        if(!$saved){
            Log::error("Cannot create record in users table");
            throw new \Exception("Cannot save User");
        }

        // assign groups
        if (!empty($this->groupsId)){
            $user->groups()->attach($this->groupsId);
        }

        // assign roles
        if (!empty($this->roleIds)){
            $user->roles()->attach($this->roleIds);
        }

        // newly created user - dispatch a welcome notification via support
        SupportNotification::dispatchWelcomeNotification($user);
        return $user;
	}


}
