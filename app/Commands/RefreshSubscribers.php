<?php namespace Phonex\Commands;

use Carbon\Carbon;
use Illuminate\Contracts\Bus\SelfHandling;
use Log;
use Phonex\License;
use Phonex\User;
use Queue;

/**
 * Refresh subscriber table from existing licenses (start time, expiration time, license type)
 * Class RefreshSubscribers
 * @package Phonex\Commands
 */
class RefreshSubscribers extends Command implements SelfHandling {

    public function __construct(){
    }

    // TODO TDD!! update test
	public function handle(){
        Log::info('RefreshSubscribers is started - turned off, but logging');
        $chunk = 20;
        $users = User::where('username','wtest63')->get();

//        User::with(['licenses', 'subscriber'])->chunk($chunk, function($users){
            foreach ($users as $user){
                if(!$user->subscriber){
                    continue;
                }
                $license = $user->getActiveLicenseWithLatestExpiration();

                if ($license != null){
                    $subscriber = $user->subscriber;
//
//                    // TODO check that expiration && start is different
                    $subscriber->issued_on = $license->starts_at;
                    $subscriber->expires_on = $license->expires_at;
                    $subscriber->license_type = $license->licenseFuncType->name;
                    $subscriber->save();
                    Queue::push('licenseUpdated', ['username'=>$user->email], 'users');
                }
            }
//        });
	}
}