<?php namespace Phonex\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Phonex\Http\Requests;
use Phonex\LicenseFuncType;
use Phonex\User;
use Phonex\Utils\DateRangeValidator;
use Phonex\Utils\Stats;

class StatsController extends Controller {

	public function QaController(){
	}

    public function getNewUsers(Request $request)
    {
        // TODO doesn't work on production! why?
//        $this->validate($request, [
//            'daterange' => 'sometimes|date_range: "Y-m-d"',
//        ]);

        // by default look for one week old users
        $dateTo = Carbon::now();
        $dateFrom = Carbon::now()->subDays(7);

        if ($request->has('daterange')){
            list($dateFrom, $dateTo) = DateRangeValidator::retrieveDates($request->get('daterange'));
        }

        // Load func types
        $filteredLicFuncTypeIds = $request->get('lic_func_type_ids', []);
        $licFuncTypes = $this->loadLicFuncTypes($filteredLicFuncTypeIds);

        $users = User::with('subscriber')
            ->where('dateCreated', '<=', $dateTo)
            ->where('dateCreated' , '>=', $dateFrom)
            ->orderBy('dateCreated', 'DESC')
            ->get();
        $users = $this->filterUsersByLicFuncTypes($users, $filteredLicFuncTypeIds);

        $daterange = $dateFrom->toDateString() . " : " . $dateTo->toDateString();
        return view('stats.new-users', compact('licFuncTypes', 'users','daterange'));
    }

    // options:
    /*
     * 1. expires in a week
     * 2. expires in two weeks
     * 3. expired last week
     * */
    public function getExpiring(Request $request)
    {
        // TODO turn this on
//        $this->validate($request, [
//            'daterange' => 'sometimes|date_range: "Y-m-d"',
//        ]);

        $dateTo = Carbon::now()->addDays(6);
        $dateFrom = Carbon::now();

        if ($request->has('daterange')){
            list($dateFrom, $dateTo) = DateRangeValidator::retrieveDates($request->get('daterange'));
        }

        // Load func types
        $filteredLicFuncTypeIds = $request->get('lic_func_type_ids', []);
        $licFuncTypes = $this->loadLicFuncTypes($filteredLicFuncTypeIds);

        // join here is only for ability of directly filtering expiration dates of active licenses
        $users = User::select(['users.*', 'licenses.expires_at'])
            ->join('licenses','users.active_license_id', '=', 'licenses.id')
            ->where('licenses.expires_at', '<=', $dateTo)
            ->where('licenses.expires_at' , '>=', $dateFrom)
            ->with('licenses')
            ->orderBy('licenses.expires_at', 'ASC')
            ->get();
        $users = $this->filterUsersByLicFuncTypes($users, $filteredLicFuncTypeIds);

        $daterange = $dateFrom->toDateString() . " : " . $dateTo->toDateString();
        return view('stats.expiring', compact('licFuncTypes', 'users','daterange'));
    }

    private function filterUsersByLicFuncTypes(Collection $users, array $filteredLicFuncTypeIds)
    {
        return $users->filter(function($user) use ($filteredLicFuncTypeIds){
            if (!$user->subscriber){
                // we want only users with subscribers
                return false;
            }

            if ($filteredLicFuncTypeIds && !in_array($user->activeLicense->license_func_type_id, $filteredLicFuncTypeIds)){
                // filter out unwanted licenses
                return false;
            }
            return true;
        });
    }

    private function loadLicFuncTypes(array $filteredFuncTypeIds)
    {
        $licFuncTypes = LicenseFuncType::all();
        foreach($licFuncTypes as $funcType){
            $funcType->selected = in_array($funcType->id, $filteredFuncTypeIds);
        }
        return $licFuncTypes;
    }

    public function getUsersStatistics(Stats $stats)
    {
        $counts = $stats->newUsersPer(15, Stats::WEEK);
        $labels = $stats->labelsPer(15, Stats::WEEK);

        // Prepare for JS print
        $labels = array_map(function($item){
            return '"' . $item . '"';
        }, $labels);

        return view('stats.users-graphs', compact('counts', 'labels'));
    }

    public function getLastActivity()
    {

    }

    public function getDevices()
    {

    }

}
