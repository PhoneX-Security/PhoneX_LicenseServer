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

class StatsController extends Controller {

	public function QaController(){
	}

    public function getNewUsers(Request $request)
    {
        $this->validate($request, [
            'daterange' => 'sometimes|date_range: "Y-m-d"',
        ]);

        // by default look for one week old users
        $dateTo = Carbon::now();
        $dateFrom = Carbon::now()->subDays(6);

        if ($request->has('daterange')){
            list($dateFrom, $dateTo) = array_map(function($item){
                return carbonFromInput(trim($item), 'Y-m-d');
            }, explode(":", $request->get('daterange', "Y-m-d")) );
        }

        $filteredFuncTypeIds = $filteredFuncTypes = [];
        if ($request->has('lic_func_type_ids')){
            $filteredFuncTypeIds = $request->get('lic_func_type_ids');
            $filteredFuncTypes = LicenseFuncType::whereIn('id', $filteredFuncTypeIds)
                ->get()
                ->pluck('name')
                ->toArray();
        }

        $users = User::with('subscriber')
            ->where('dateCreated', '<=', $dateTo)
            ->where('dateCreated' , '>=', $dateFrom)
            ->orderBy('dateCreated', 'DESC')->get();

        $users = $users->filter(function($user) use ($filteredFuncTypes){
            if (!$user->subscriber){
                // we want only users with subscribers
                return false;
            }

            if ($filteredFuncTypes && !in_array($user->subscriber->license_type, $filteredFuncTypes)){
                // filter out unwanted licenses
                return false;
            }
            return true;

        });

        $licFuncTypes = LicenseFuncType::all();
        foreach($licFuncTypes as $funcType){
            $funcType->selected = in_array($funcType->id, $filteredFuncTypeIds);
        }
        $daterange = $dateFrom->toDateString() . " : " . $dateTo->toDateString();
        return view('stats.new-users', compact('licFuncTypes', 'users','daterange'));

    }

    public function getLastActivity()
    {

    }

    public function getExpiring()
    {

    }

    public function getDevices()
    {

    }

}
