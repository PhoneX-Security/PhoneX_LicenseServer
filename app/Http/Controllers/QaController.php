<?php namespace Phonex\Http\Controllers;

use Bus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Phonex\Jobs\CreateSubscriberWithLicense;
use Phonex\Jobs\CreateUser;
use Phonex\Jobs\RefreshSubscribers;
use Phonex\ContactList;
use Phonex\Http\Requests;
use Phonex\LicenseFuncType;
use Phonex\LicenseType;
use Phonex\Subscriber;
use Phonex\User;
use Queue;

class QaController extends Controller {
	public function QaController(){
	}

    public function getCheckLic(){
        $c = new RefreshSubscribers();
        Bus::dispatch($c);
    }

	public function getChangeExpiration(Request $request){
        $dt = Carbon::now();

        if ($request->has('m')){
            $dt = $dt->addMinutes($request->get('m'));
        }
        if ($request->has('h')){
            $dt = $dt->addHours($request->get('h'));
        }
        if ($request->has('d')){
            $dt = $dt->addDays($request->get('d'));
        }


        $name  = "test322";
        $subscriber = Subscriber::where('username', $name)->first();

        $subscriber->expires_on = $dt->toDateTimeString();
        $subscriber->save();
        Queue::push('licenseUpdated', ['username' => $name."@phone-x.net"], 'users');
        dd('nasrat');
	}

    public function getCleanSupportAccountCl(Request $request){
        dd('turned off');

        $sub = User::getSupportUser()->subscriber;

        $time = Carbon::now()->subDays(2);

        $contacts = Subscriber::select('subscriber.*')
            ->join('contactlist', 'subscriber.id', '=', 'contactlist.int_usr_id')
            ->where('contactlist.subscriber_id', $sub->id)

//            ->where('expires_on', '<', $time->toDateTimeString())
//            ->whereNull('date_first_login')
            ->where('subscriber.username','LIKE','%vymaztemamatej%')
            ->get();

        dd($contacts);

//        $count = count($contacts);
        $count = 0;
        foreach($contacts as $c){
            $sub->removeFromContactList($c);
            $c->removeFromContactList($sub);
            $count++;
        }
        return 'all deleted users removed from support account cl (' . $count . ')';
    }

    public function getCreateDusanTestEnv(Request $request){
        die('turned off');
        $users = User::orderBy('id', 'DESC')->limit(510)->get();

        $licType = LicenseType::find(2); // year
        $licFuncType = LicenseFuncType::getFull();
        $masterName = 'qa_dusan_master';
//        $slaveNamePrefix = 'qa_dusan_slave';
        $pass = 'gragbadd0';

        $masterUser = Bus::dispatch(new CreateUser($masterName));
        Bus::dispatch(new CreateSubscriberWithLicense($masterUser, $licType, $licFuncType, $pass));

        foreach($users as $u){
            $masterUser->addToContactList($u);
        }
        return 'success';
    }

    public function getAddContact(Request $request){
        die('turned off');
        $username = $request->get('username');

        $master =  User::findByUsername('qa_dusan_master');
        $user = User::findByUsername($username);

        if (!$username || !$user){
            return 'user does not exist';
        }

        $master->addToContactList($user);
        return 'user added to contact list';
    }


    public function getMigrateTurn(Request $request){
        die('turned off');

        $subs = Subscriber::whereNull('turn_passwd_ha1b')->get();
//        dd($subs);
//        $subs = Subscriber::all();
        foreach($subs as $subscriber){
//            $subscriber->turnPasswd = getRandomString(24);
//            $subscriber->turn_passwd_ha1 = getHA1_1($subscriber->username . '@' . $subscriber->domain, $subscriber->turnPasswd);
            $subscriber->turn_passwd_ha1b = getHA1_B($subscriber->username . '@' . $subscriber->domain, $subscriber->turnPasswd);
            $subscriber->save();
        }

        return 'turn passwords migrated';
    }
}
