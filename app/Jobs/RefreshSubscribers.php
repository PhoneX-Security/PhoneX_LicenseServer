<?php namespace Phonex\Jobs;

use Carbon\Carbon;
use Illuminate\Contracts\Bus\SelfHandling;
use Log;
use Phonex\License;
use Phonex\User;
use Queue;

/**
 * Refresh subscriber table from existing licenses (start time, expiration time, license type)
 * This is executed by cron job at night
 * Class RefreshSubscribers
 * @package Phonex\Jobs
 */
class RefreshSubscribers extends Command implements SelfHandling {

    public function __construct(){
    }

	public function handle(){
        Log::info('RefreshSubscribers is started');
        $chunk = 20;
        $counter = 0;

        User::with(['licenses', 'subscriber'])->chunk($chunk, function($users) use ($counter){
            foreach ($users as $user){
                if(!$user->subscriber){
                    continue;
                }
                self::refreshSingleUser($user);

            }
        });

        Log::info('RefreshSubscribers is finished');

        // counter doesn't work - is not updated from closure
//        Log::info('RefreshSubscribers is finished, ' . $counter . " users were updated.");
	}

    public static function refreshSingleUser(User $user, $sendPushNotification = true){
        /*
                We want to update Subscriber table (in opensips database) together with auxiliary fields in users table with issued_on, expires_on and license type things.
                As user may have multiple licenses, we want to find out the recent one, that should be used.
                Strategy:
                1. Find active license with latest expiration
                2. If no such license is present, search future licenses and find one with earliest start
                3. If none of previous points were able to find licenses, find the past license that expired most recently
                4. With the license find, update above mentioned information for subscriber and send push notification (but only if data has changed, to avoid spamming users)
                 */

        $license = self::getActiveLicenseWithLatestExpiration($user);
        if ($license == null){
            $license = self::getFutureLicenseWithEarliestStart($user);
        }
        if ($license == null){
            $license = self::getPastLicenseWithLatestExpiration($user);
        }

        if ($license != null){
            // If auxiliary user columns are empty, fill them out
            if (!$user->activeLicense){
                $user->active_license_id = $license->id;
                $user->save();
            }

            $subscriber = $user->subscriber;

            // Checking non-equality of Carbon instances or string change of license type
            if (!$subscriber->issued_on
                || !$subscriber->expires_on
                || $subscriber->issued_on->ne($license->starts_at)
                || $subscriber->expires_on->ne($license->expires_at)
                || $subscriber->license_type !== $license->licenseFuncType->name){

                // something has changed, update subscribers table
                $subscriber->issued_on = $license->starts_at;
                $subscriber->expires_on = $license->expires_at;
                $subscriber->license_type = $license->licenseFuncType->name;
                $subscriber->save();
//
//                        // also update user's auxiliary column
                $user->active_license_id = $license->id;
                $user->save();

                Log::info('RefreshSubscribers updating user: ' . $user->username);

                // TODO enable this when we have confidence this function works well
                if($sendPushNotification){
                    Queue::push('licenseUpdated', ['username'=>$user->email], 'users');
                }
            }
        }
    }

    public static function getActiveLicenseWithLatestExpiration(User $user)
    {
        if ($user->licenses->isEmpty()){
            return null;
        }
        $now = Carbon::now();
        $activeLicenses = $user->licenses->filter(function ($lic) use ($now) {
            // returns true or false
            return ($lic->starts_at->lte($now) && $lic->expires_at->gte($now));
        });

        if ($activeLicenses->isEmpty()){
            return null;
        }
        $activeLic = $activeLicenses->first();
        foreach($activeLicenses as $lic){
            $activeLic = License::endingLater($activeLic, $lic);
        }

        return $activeLic;
    }

    public static function getFutureLicenseWithEarliestStart(User $user)
    {
        if ($user->licenses->isEmpty()){
            return null;
        }
        $now = Carbon::now();
        $futureLicenses = $user->licenses->filter(function ($lic) use ($now) {
            // returns true of false
            return $lic->starts_at->gt($now);
        });
        if ($futureLicenses->isEmpty()){
            return null;
        }
        $lic = $futureLicenses->first();
        foreach($futureLicenses as $futureLicense){
            $lic = License::startingEarlier($lic, $futureLicense);
        }
        return $lic;
    }

    public static function getPastLicenseWithLatestExpiration(User $user)
    {
        if ($user->licenses->isEmpty()){
            return null;
        }
        $now = Carbon::now();
        $pastLicenses = $user->licenses->filter(function ($lic) use ($now) {
            // returns true of false
            return $lic->expires_at->lt($now);
        });
        if ($pastLicenses->isEmpty()){
            return null;
        }
        $lic = $pastLicenses->first();
        foreach($pastLicenses as $pastLicense){
            $lic = License::endingLater($lic, $pastLicense);
        }
        return $lic;
    }
}