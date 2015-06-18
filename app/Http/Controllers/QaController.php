<?php namespace Phonex\Http\Controllers;

use Bus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Phonex\Commands\CreateSubscriberWithLicense;
use Phonex\Commands\CreateUser;
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

//        $name  = "test318";
        $name  = "jhuska4";
//        dd($dt);
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
        $username = $request->get('username');

        $master =  User::findByUsername('qa_dusan_master');
        $user = User::findByUsername($username);

        if (!$username || !$user){
            return 'user does not exist';
        }

        $master->addToContactList($user);
        return 'user added to contact list';
    }
}
