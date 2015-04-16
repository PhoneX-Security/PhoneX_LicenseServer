<?php namespace Phonex\Commands;

use Carbon\Carbon;
use Illuminate\Contracts\Bus\SelfHandling;
use Phonex\License;
use Phonex\LicenseFuncType;
use Phonex\LicenseType;
use Phonex\User;

class IssueLicense extends Command implements SelfHandling {
    private $user;
    private $licenseType;
    private $licenseFuncType;

    public function __construct(User $user, LicenseType $licenseType, LicenseFuncType $licenseFuncType){
        $this->user = $user;
        $this->licenseType = $licenseType;
        $this->licenseFuncType = $licenseFuncType;
    }

	public function handle(){
        $subscriber = $this->user->subscriber;
        if (!$subscriber){
            throw new \Exception("Cannot issue license for user with no subscriber record");
        }

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
        $license->save();

        // update subscriber
        $subscriber->issued_on = $startsAt;
        $subscriber->expires_on = $expiresAt;
        $subscriber->license_type = $this->licenseFuncType->name;
        $subscriber->save();

        return $license;
	}
}