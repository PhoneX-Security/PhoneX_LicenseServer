<?php namespace Phonex\Commands;

use Carbon\Carbon;
use Illuminate\Contracts\Bus\SelfHandling;
use Phonex\Events\AuditEvent;
use Phonex\License;
use Phonex\LicenseFuncType;
use Phonex\LicenseType;
use Phonex\Subscriber;
use Phonex\User;

class CreateSubscriberWithLicense extends Command implements SelfHandling {
    private $user;
    private $licenseType;
    private $licenseFuncType;
    private $sipPassword;

    // publicly settable attributes
    public $comment;

    public function __construct(User $user, LicenseType $licenseType, LicenseFuncType $licenseFuncType, $sipPassword){
        $this->user = $user;
        $this->licenseType = $licenseType;
        $this->licenseFuncType = $licenseFuncType;
        $this->sipPassword = $sipPassword;
    }

	public function handle(){
        $startsAt = Carbon::now()->toDateTimeString();
        $c_expiresAt = Carbon::now()->addDays($this->licenseType->days);
        $expiresAt = $c_expiresAt->toDateTimeString();

        // create license
        $license = new License();
        $license->user_id = $this->user->id;
        $license->license_type_id = $this->licenseType->id;
        $license->license_func_type_id = $this->licenseFuncType->id;
        $license->starts_at = $startsAt;
        $license->expires_at = $expiresAt;
        if ($this->comment){
            $license->comment=$this->comment;
        }

        $license->save();
        event(AuditEvent::create('license', $license->id));


        // Create a new user on the SOAP server
        $subscriber = Subscriber::createSubscriber($this->user->username, $this->sipPassword, $startsAt, $expiresAt, $this->licenseFuncType->name);
        $subscriber->save();

        $this->user->subscriber_id = $subscriber->id;
        $this->user->save();

        return $license;
	}
}