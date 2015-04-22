<?php namespace Phonex\Commands;

use Carbon\Carbon;
use Illuminate\Contracts\Bus\SelfHandling;
use Log;
use Phonex\License;
use Phonex\User;

/**
 * Refresh subscriber table from existing licenses (start time, expiration time, license type)
 * Class RefreshSubscribers
 * @package Phonex\Commands
 */
class RefreshSubscribers extends Command implements SelfHandling {

    public function __construct(){
    }

	public function handle(){
        Log::info('RefreshSubscribers is started');
        // Process users in chunks
        $chunk = 20;
        User::with(['licenses', 'subscriber'])->chunk($chunk, function($users){
            foreach ($users as $user){
                if(!$user->subscriber){
                    continue;
                }
                $license = $this->getRecentLicense($user);
                if ($license != null){
                    $subscriber = $user->subscriber;
                    $subscriber->issued_on = $license->starts_at;
                    $subscriber->expires_on = $license->expires_at;
                    $subscriber->license_type = $license->licenseFuncType->name;
                    $subscriber->deleted = 0;

                    $subscriber->save();
                }
            }
        });
	}

    /**
     * Get active or future active license (starting as early as possible)
     * @param User $user
     * @return null
     */
    public static function getRecentLicense(User $user){
        if ($user->licenses->count() == 0) {
            return null;
        }

        // first determinate what is active or not
        $lics = $user->licenses->map(function ($lic) {
            $now = Carbon::now();

            if ($lic->starts_at->lte($now) && $lic->expires_at->gte($now)) {
                $lic->active = true;
            } else {
                $lic->active = false;
            }
            return $lic;
        });

        $futureLic = null;

        foreach($lics as $lic){
            if ($lic->active){
                // there should be only one active license, so if we find it, return it
                return $lic;
            }

            if ($lic->starts_at->isFuture()){
                $futureLic = self::startingEarlier($futureLic, $lic);
            }
        }

        return $futureLic;
    }

    private static function startingEarlier($lic1, License $lic2){
        if ($lic1 == null){
            return $lic2;
        }

        return $lic2->starts_at->gte($lic1->starts_at) ? $lic1 : $lic2;
    }
}