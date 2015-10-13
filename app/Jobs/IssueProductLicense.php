<?php namespace Phonex\Jobs;

use Carbon\Carbon;
use Illuminate\Contracts\Bus\SelfHandling;
use Phonex\License;
use Phonex\Model\Product;
use Phonex\User;

class IssueProductLicense extends Command implements SelfHandling {
    private $user;
    private $product;
    private $comment;
    private $startsAt;

    public function __construct(User $user, Product $product)
    {
        $this->user = $user;
        $this->product = $product;
    }

    public function startingAt(Carbon $startsAt)
    {
        $this->startsAt = $startsAt->copy();
        return $this;
    }

    public function setComment($comment)
    {
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

        // reset to start of a day
        $this->startsAt = $this->startsAt->startOfDay();
        $expiresAt = $this->product->computeExpirationTime($this->startsAt);

        // create license
        $license = new License();
        $license->user_id = $this->user->id;
        $license->product_id = $this->product->id;
        $license->starts_at = $this->startsAt;
        $license->expires_at = $expiresAt;
        if ($this->comment){
            $license->comment = $this->comment;
        }

        // legacy db references (these values can be retrieved via product)
        $license->license_type_id = $this->product->licenseType->id;
        $license->license_func_type_id = $this->product->licenseFuncType->id;

        if (\Auth::user() && \Auth::user()->id){
            $license->issuer_id = \Auth::user()->id;
        }

        $license->save();

        // refresh usage_policy_current and usage_policy_expired in subscribers table
        RefreshSubscribers::refreshSingleUser($this->user, true);

        return $license;
	}
}
