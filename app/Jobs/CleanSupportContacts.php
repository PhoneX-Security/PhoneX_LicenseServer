<?php namespace Phonex\Jobs;

use Carbon\Carbon;
use Illuminate\Contracts\Bus\SelfHandling;
use Log;
use Phonex\Subscriber;
use Phonex\User;
use Queue;

/**
 * Clean support contact list to make it manageable to use the app
 * @package Phonex\Jobs
 */
class CleanSupportContacts extends Command implements SelfHandling {

    public function __construct(){
    }

	public function handle(){
        Log::info('CleanSupportContacts is started');
        $this->clean();
        Log::info('CleanSupportContacts is finished');
	}

    private function clean()
    {
        $supportUser = User::getSupportUser();
        $supportSubscriber = $supportUser->subscriber;
//        $time = Carbon::now()->subDays(45);
        // Remove contacts that weren't logged in 22 days from support (they are automatically re-added when logged in again)
        // 22 days - Zbirka constant
        // https://www.youtube.com/watch?v=ZVoNgOyHWgg
        $timeLastActivity = Carbon::now()->subDays(22);

        $contacts = Subscriber::select('subscriber.*')
            ->join('contactlist', 'subscriber.id', '=', 'contactlist.int_usr_id')
            ->where('contactlist.subscriber_id', $supportSubscriber->id)
            ->whereNotNull('date_last_activity')
            ->where('date_last_activity', '<', $timeLastActivity->toDateTimeString())

//            ->where('license_type', 'trial')
//            ->where('expires_on', '<', $time->toDateTimeString())
//            ->whereNull('date_first_login')
//            ->where('subscriber.username','LIKE','%honza%')
            ->get();

//        $count = count($contacts);
        $count = 0;
        foreach($contacts as $c){
            Log::info("CleanSupportContacts - removing", [$c->username]);
            $supportSubscriber->removeFromContactList($c);
//            $c->removeFromContactList($supportSuscriber);
            $count++;
        }
        if ($count > 0){
            try {
                Queue::push('ContactListUpdated', ['username'=> $supportUser->email], 'users');
            } catch (\Exception $e){
                Log::error('cannot push ContactListUpdated message', [$e]);
            }
        }
        Log::info('CleanSupportContacts - number of removed contacts from support cl is ' . $count);
    }
}