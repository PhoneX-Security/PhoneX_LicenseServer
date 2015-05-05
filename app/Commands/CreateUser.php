<?php namespace Phonex\Commands;

use Carbon\Carbon;
use Log;

use Illuminate\Contracts\Bus\SelfHandling;
use Phonex\ContactList;
use Phonex\Events\AuditEvent;
use Phonex\License;
use Phonex\LicenseType;
use Phonex\Subscriber;
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


    public function __construct($username, $groupsId = array(), $isQaTrial = false, $trialNumber = null){
        $this->username = $username;
        $this->groupsId = $groupsId;
        $this->isQaTrial = $isQaTrial;
        $this->trialNumber = $trialNumber;
    }

    public function addAccess($password){
        $this->password = $password;
        return $this;
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
            $user->has_access = 1;
            $user->password = bcrypt($this->password);
        }
        if ($this->subscriberId){
            $user->subscriber_id = $this->subscriberId;
        }


        $saved = $user->save();
        event(AuditEvent::create('user', $user->id));

        // allow user to try again
        if(!$saved){
            Log::error("Cannot create record in PhoneX_users table");
            throw new \Exception("Cannot save User");
        }

        // assign groups
        if (!empty($this->groupsId)){
            $user->groups()->attach($this->groupsId);
        }

        return $user;
	}
}
