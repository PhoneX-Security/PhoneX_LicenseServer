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

//        $startsAt = Carbon::now()->toDateTimeString();
//        $c_expiresAt = Carbon::now()->addDays($this->licenseType->days);
//        $expiresAt = $c_expiresAt->toDateTimeString();
//
//        $license = new License();
//        $license->user_id = $user->id;
//        $license->license_type_id = $this->licenseType->id;
//        $license->starts_at = $startsAt;
//        $license->expires_at = $expiresAt;
//        $license->save();
//
//
//        // Create a new user on the SOAP server
//        $subscriber = Subscriber::createSubscriber($user->username, $this->password, $startsAt, $expiresAt);
//        $savedSipUser = $subscriber->save();
//
//        $user->subscriber_id = $subscriber->id;
//        $user->save();
//
//        // if sip user creation fails, allow to try again
//        if (!$savedSipUser) {
//            Log::error("Cannot create subscriber in SOAP subscriber list.");
//            throw new \Exception("Cannot save User");
//        }



        // add support to contact list
//        if (!$this->isQaTrial && $this->addSupportContact){
//            ContactList::addSupportToContactListMutually($user);
//        }

//        return $user;
	}

}
