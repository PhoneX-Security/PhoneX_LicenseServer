<?php namespace Phonex\Commands;

use Carbon\Carbon;
use Illuminate\Contracts\Bus\SelfHandling;
use Phonex\License;
use Phonex\LicenseFuncType;
use Phonex\LicenseType;
use Phonex\User;
use Queue;

class IssueLicense extends Command implements SelfHandling {
    private $user;
    private $licenseType;
    private $licenseFuncType;
    private $startsAt;
    private $comment;

    public function __construct(User $user, LicenseType $licenseType, LicenseFuncType $licenseFuncType){
        $this->user = $user;
        $this->licenseType = $licenseType;
        $this->licenseFuncType = $licenseFuncType;
    }

    public function startingAt(Carbon $startsAt){
        $this->startsAt = $startsAt->copy();
        return $this;
    }

    public function setComment($comment){
        $this->comment = $comment;
        return $this;
    }

	public function handle(){
        $subscriber = $this->user->subscriber;
        if (!$subscriber){
            throw new \Exception("Cannot issue license for user with no subscriber record");
        }


        // if not set, license starts now
        if (!$this->startsAt){
            $this->startsAt = Carbon::now();
        }

        $startsAt = $this->startsAt->toDateTimeString();
        $c_expiresAt = $this->startsAt->addDays($this->licenseType->days);
        $expiresAt = $c_expiresAt->toDateTimeString();

        // create license
        $license = new License();
        $license->user_id = $this->user->id;
        $license->license_type_id = $this->licenseType->id;
        $license->license_func_type_id = $this->licenseFuncType->id;
        $license->starts_at = $startsAt;
        $license->expires_at = $expiresAt;
        if ($this->comment){
            $license->comment = $this->comment;
        }

        $license->save();

        // update subscriber
        $subscriber->issued_on = $startsAt;
        $subscriber->expires_on = $expiresAt;
        $subscriber->license_type = $this->licenseFuncType->name;

        // in case flag deleted is turned on, turn it off
        $subscriber->deleted = 0;

        $subscriber->save();

        Queue::push('licenseUpdated', ['username'=>$this->user->email], 'users');
        return $license;
	}
}
