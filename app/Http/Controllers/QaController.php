<?php namespace Phonex\Http\Controllers;

use Bus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Phonex\Http\Requests;
use Phonex\Jobs\RefreshSubscribers;
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

    public function getCleanTestAccounts(){
        dd('turned off');
        $usernames = ['qatrial801', 'qatrial802', 'qatrial803', 'qatrial804', 'qatrial805'];
//        ,
        foreach($usernames as $username){
            $user = User::findByUsername($username);
            if ($user)
                $user->deleteWithLicenses();
        }
        var_dump('deleted ' . count($usernames));
    }

    public function getUpdateExports()
    {
        $users = User::whereNotNull('business_code_id')->get();
//
//        foreach($users as $user){
//            if ($user->licenses->count() > 1){
//
//            }
//
////            dd($user);
//            $lic = $user->licenses[0];
//            $lic->business_code_id = $user->business_code_id;
//            $lic->save();
//        }

//        $exports = BusinessCodesExport::all();
//        foreach($exports as $export){
//            $codes = $export->codes;
//            if ($codes && $codes->count()>0){
//                $code = $codes[0];
//
//                if ($code->parent_id){
//                    $export->parent_id = $code->parent_id;
//                }
//                if ($code->group_id){
//                    $export->group_id = $code->group_id;
//                }
//                if ($code->license_type_id && $code->license_type_id!=0){
//                    $export->license_type_id = $code->license_type_id;
//                }
//                if ($code->license_func_type_id && $code->license_func_type_id!=0){
//                    $export->license_func_type_id = $code->license_func_type_id;
//                }
//                if ($code->users_limit && $code->users_limit!=0){
//                    $export->license_limit_per_code = $code->users_limit;
//                }
////                dd([$code,$export]);
//
//                $export->save();
//            }
//        }
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


        $name  = "jhuska4";
        $subscriber = Subscriber::where('username', $name)->first();

        $subscriber->expires_on = $dt->toDateTimeString();
        $subscriber->save();
        Queue::push('licenseUpdated', ['username' => $name."@phone-x.net"], 'users');
        dd('nasrat');
	}

    public function getCleanSupportAccountCl(Request $request){
        dd('turned off');

        $sub = User::getSupportUser()->subscriber;

        $time = Carbon::now()->subDays(45);
        $timeLastActivity = Carbon::now()->subDays(30);

        $contacts = Subscriber::select('subscriber.*')
            ->join('contactlist', 'subscriber.id', '=', 'contactlist.int_usr_id')
            ->where('contactlist.subscriber_id', $sub->id)

            ->where('license_type', 'trial')
            ->where('expires_on', '<', $time->toDateTimeString())
            ->whereNotNull('date_last_activity')
            ->where('date_last_activity', '<', $timeLastActivity->toDateTimeString())
//            ->whereNull('date_first_login')
//            ->where('subscriber.username','LIKE','%honza%')
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

    //Route::get('test_connect', function(){
////    $prefix = "miro";
////    $masterNum = 5;
////    $poolNum = 20;
////
////    $masters = [];
////    $pools = [];
////
////    for ($i=1; $i<=5; $i++){
////        $masters[] = $prefix . '0' . $i;
////    }
////
////    for ($i=6; $i<=9; $i++){
////        $pools[] = $prefix . '0' . $i;
////    }
////
////    for ($i=10; $i<=25; $i++){
////        $pools[] = $prefix . $i;
////    }
////
////    $mainUser = User::where('username', 'miro01')->first();
////    $masterUsers = [];
////    $poolUsers = [];
////
////    foreach($masters as $name){
////        $masterUsers[] = User::where('username', $name)->first();
////    }
////
////    foreach($pools as $name){
////        $poolUsers[] = User::where('username', $name)->first();
////    }
////
//////    // pool licenses have only main user
//////    foreach($poolUsers as $user){
//////        ContactList::addUsersToContactListMutually($mainUser, $user);
//////    }
////
////    foreach($masterUsers as $u){
////        foreach($masterUsers as $uu){
////            if ($u != $uu){
//////                echo $u->username . ' + ' . $uu->username . '<br />';
////                $u->addToContactList($uu);
////            }
////        }
////    }
//});
}
