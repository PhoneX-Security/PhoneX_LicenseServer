<?php namespace Phonex\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Phonex\Http\Requests;
use Phonex\Subscriber;
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

        $name  = "test318";
//        dd($dt);
        $subscriber = Subscriber::where('username', $name)->first();

        $subscriber->expires_on = $dt->toDateTimeString();
        $subscriber->save();
        Queue::push('licenseUpdated', ['username' => $name."@phone-x.net"], 'users');
        dd($subscriber->expires_on);
	}
}
