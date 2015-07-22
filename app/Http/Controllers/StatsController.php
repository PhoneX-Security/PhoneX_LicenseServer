<?php namespace Phonex\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Phonex\Http\Requests;
use Phonex\LicenseFuncType;
use Phonex\LicenseType;
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

        // Load licenses types
        $filteredLicTypeIds = $request->get('lic_type_ids', []);
        $licTypes = $this->loadLicTypes($filteredLicTypeIds);

        $filteredLicFuncTypeIds = $request->get('lic_func_type_ids', []);
        $licFuncTypes = $this->loadLicFuncTypes($filteredLicFuncTypeIds);

         $query = User::select(['users.*', 'licenses.license_func_type_id', 'licenses.license_type_id'])
            ->join('licenses','users.active_license_id', '=', 'licenses.id')
            ->where('dateCreated', '<=', $dateTo)
            ->where('dateCreated' , '>=', $dateFrom)
            ->with(['subscriber','licenses', 'licenses.licenseType', 'licenses.licenseFuncType'])
            ->orderBy('dateCreated', 'DESC');

        if ($filteredLicTypeIds){
            $query = $query->whereIn('license_type_id', $filteredLicTypeIds);
        }
        if ($filteredLicFuncTypeIds){
            $query = $query->whereIn('license_func_type_id', $filteredLicFuncTypeIds);
        }

        $users = $query->get();
        $users = $this->filterUsersWithSubscriber($users, $filteredLicFuncTypeIds);

        $daterange = $dateFrom->toDateString() . " : " . $dateTo->toDateString();

        return view('stats.new-users', compact('licTypes', 'licFuncTypes', 'users','daterange'));
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

        // Load licenses types
        $filteredLicTypeIds = $request->get('lic_type_ids', []);
        $licTypes = $this->loadLicTypes($filteredLicTypeIds);

        $filteredLicFuncTypeIds = $request->get('lic_func_type_ids', []);
        $licFuncTypes = $this->loadLicFuncTypes($filteredLicFuncTypeIds);

        // join here is only for ability of directly filtering expiration dates of active licenses
        $query = User::select(['users.*', 'licenses.license_func_type_id', 'licenses.license_type_id'])
            ->join('licenses','users.active_license_id', '=', 'licenses.id')
            ->where('licenses.expires_at', '<=', $dateTo)
            ->where('licenses.expires_at' , '>=', $dateFrom)
            ->with(['subscriber','licenses', 'licenses.licenseType', 'licenses.licenseFuncType'])
            ->orderBy('licenses.expires_at', 'ASC');

        if ($filteredLicTypeIds){
            $query = $query->whereIn('license_type_id', $filteredLicTypeIds);
        }
        if ($filteredLicFuncTypeIds){
            $query = $query->whereIn('license_func_type_id', $filteredLicFuncTypeIds);
        }

        $users = $query->get();
        $users = $this->filterUsersWithSubscriber($users, $filteredLicFuncTypeIds);

        $daterange = $dateFrom->toDateString() . " : " . $dateTo->toDateString();
        return view('stats.expiring', compact('licTypes', 'licFuncTypes', 'users','daterange'));
    }

    private function filterUsersWithSubscriber(Collection $users, array $filteredLicFuncTypeIds)
    {
        return $users->filter(function($user) use ($filteredLicFuncTypeIds){
            if (!$user->subscriber){
                // we want only users with subscribers
                return false;
            }
//            if ($filteredLicFuncTypeIds && !in_array($user->activeLicense->license_func_type_id, $filteredLicFuncTypeIds)){
//                // filter out unwanted licenses
//                return false;
//            }
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

    private function loadLicTypes(array $filteredTypeIds)
    {
        $licTypes = LicenseType::all();
        foreach($licTypes as $funcType){
            $funcType->selected = in_array($funcType->id, $filteredTypeIds);
        }
        return $licTypes;
    }

    public function getUsersStatistics(Stats $stats)
    {
        $counts = $stats->newUsersPer(16, Stats::WEEK);
        $labels = $stats->labelsPer(16, Stats::WEEK);

        // Prepare for JS print
        $labels = array_map(function($item){
            return '"' . $item . '"';
        }, $labels);

        return view('stats.users-graphs', compact('counts', 'labels'));
    }

    public function getTextReport(Request $request, Stats $stats)
    {
        // subtract to be day ago - end of sunday
        $dateTo = Carbon::parse("next monday")->subSecond();
        $dateFrom = Carbon::parse("last monday");
        if ($request->has('daterange')){
            list($dateFrom, $dateTo) = DateRangeValidator::retrieveDates($request->get('daterange'));
            $dateTo = $dateTo->endOfDay();
            $dateFrom = $dateFrom->startOfDay();
        }

        list($existingUsersData, $newUsersData) = $stats->reportPerPeriod($dateFrom, $dateTo);
        $licenseTypes = LicenseType::all()->keyBy('id');
        $licenseFuncTypes = LicenseFuncType::all()->keyBy('id');

        $daterange = $dateFrom->toDateString() . " : " . $dateTo->toDateString();
        $withUsers = $request->has('with-users');
        return view('stats.text-report', compact('existingUsersData', 'newUsersData', 'licenseTypes', 'licenseFuncTypes', 'daterange', 'withUsers'));
    }

    public function getLastActivity()
    {

    }

    public function getDevices()
    {

    }

}
