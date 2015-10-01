<?php namespace Phonex\Jobs;

use Illuminate\Contracts\Bus\SelfHandling;
use Log;
use Phonex\Events\AuditEvent;
use Phonex\Model\SupportNotification;
use Phonex\Subscriber;
use Phonex\User;

class CreateUserWithSubscriber extends Command implements SelfHandling {
    private $username;
    private $password;
    private $groupsId;
    private $roleIds;

    private $comment;
    private $hasAccess = false;


    public function __construct($username, $password){
        $this->username = $username;
        $this->password = $password;
    }

    public function addGroups(array $groupIds)
    {
        $this->groupsId = $groupIds;
    }

    public function addRoles(array $roleIds)
    {
        $this->roleIds = $roleIds;
    }

    public function addAccess()
    {
        $this->hasAccess = true;
    }

    public function addComment($comment)
    {
        $this->comment = $comment;
    }

	public function handle(){
        $user = new User();
        $user->username = $this->username;
        $user->email = $user->username . "@phone-x.net";
        $user->confirmed = 1;

        $user->password = bcrypt($this->password);
        $user->has_access = $this->hasAccess ? 1 : 0;

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

        // Create a new user on the SOAP server
        $subscriber = Subscriber::newSubscriber($this->username, $this->password);
        $subscriber->save();

        $user->subscriber_id = $subscriber->id;
        $user->save();

        // newly created user - dispatch a welcome notification via support
        SupportNotification::dispatchWelcomeNotification($user);
        return $user;
	}
}
