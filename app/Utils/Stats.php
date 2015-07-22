<?php namespace Phonex\Utils;


use Carbon\Carbon;
use Phonex\License;
use Phonex\LicenseFuncType;
use Phonex\LicenseType;
use Phonex\Model\UsageLogs;
use Phonex\User;
use stdClass;

class Stats
{
    const WEEK = "week";
    const DAY = "day";

    public function newUsersPer($totalPeriods, $periodType = self::WEEK)
    {
        $timePeriod = null;
        $dateFrom = null;

        if ($periodType === self::WEEK){
            $timePeriod = 7*24*60*60;
            $dateFrom = Carbon::now()->subWeeks($totalPeriods);
        } else if ($periodType === self::DAY){
            $timePeriod = 24*60*60;
            $dateFrom = Carbon::now()->subDays($totalPeriods);
        } else {
            throw new \Exception("unknown period type");
        }

        $users = User::select('dateCreated')->where('dateCreated', '>=', $dateFrom)->get();
        $counts = [];
        for($i = 0; $i< $totalPeriods; $i++){
            $counts[$i] = 0;
        }

        foreach($users as $u){
            $x = intval(($u->dateCreated->timestamp - $dateFrom->timestamp) / $timePeriod);
            $counts[$x] = $counts[$x] +1;
        }
        return $counts;
    }

    public function labelsPer($totalPeriods, $periodType = self::WEEK)
    {
        $dateFrom = null;
        $dateTo = null;

        if ($periodType === self::WEEK){
            $dateFrom = Carbon::now()->subWeeks($totalPeriods);
            $dateTo = Carbon::now()->subWeeks($totalPeriods - 1);
        } else if ($periodType === self::DAY){
            $dateFrom = Carbon::tomorrow()->subDays($totalPeriods);
            $dateTo = Carbon::tomorrow()->subDays($totalPeriods - 1);
        } else {
            throw new \Exception("unknown period type '{$periodType}'");
        }

        // week variant
        $labels = [];
        for($i=0; $i<$totalPeriods; $i++){
            if ($periodType === self::WEEK){

                $labels[] = $dateFrom->day . "." . $dateFrom->month . ". - " . $dateTo->day . "." . $dateTo->month;
                $dateFrom = $dateFrom->addWeek();
                $dateTo = $dateTo->addWeek();

            } else if ($periodType === self::DAY){

                $labels[] = $dateFrom->day . "." . $dateFrom->month . ".";
                $dateFrom = $dateFrom->addDay();
                $dateTo = $dateTo->addDay();
            }
        }

        return $labels;
    }

    /**
     * Only days are supported by now
     * @param User $user
     * @param $totalPeriods (in days)
     * @return array
     */
    public function userLastActivity(User $user, $totalPeriods)
    {
        /**
         * BIG WARNING:
         * all data stored in datetime columns from opensips database have currently a time drift (UTC+2), be aware!
         * Issue is created PKS-2 (if the issue is closed, please delete this)
         */
        $timeDrift = 2*60*60; // 2 hours drift has to be subtracted from all date 'n times coming from opensips db (naughty fix)

        $timePeriod = 24*60*60; // day
//        $dateFrom = Carbon::now()->subDays($totalPeriods);
        $dateFrom = Carbon::tomorrow()->subDays($totalPeriods); // counting from tomorrow - which basically means end of today

        $usageLogs = UsageLogs::where('luser', $user->email)
            ->where('lwhen', '>=', $dateFrom->toDateTimeString())
            ->get();

        $counts = [];
        for($i = 0; $i< $totalPeriods; $i++) {
            $counts[$i] = 0;
        }

        foreach($usageLogs as $u){
            $timeDiff =  $u->lwhen->timestamp - $dateFrom->timestamp - $timeDrift;
            $index = intval($timeDiff / $timePeriod);
            $counts[$index] = $counts[$index] +1;
        }
        return $counts;
    }

    public function reportPerPeriod(Carbon $dateFrom = null, Carbon $dateTo = null)
    {
        if ($dateFrom === null) {
            $dateFrom = Carbon::parse('last monday');
        }
        if ($dateTo === null) {
            $dateTo = Carbon::parse('next monday');
        }

        $lics = License::with('user', 'user.subscriber')
            ->where('created_at', '>=', $dateFrom)
            ->where('created_at', '<=', $dateTo)
            ->get();

        $users = User::select('users.id')
            ->where('dateCreated', '>=', $dateFrom)
            ->where('dateCreated', '<=', $dateTo)
            ->get();
        $newUsersIds = $users->pluck('id')->toArray();

        $newUsers = [];
        $newUsersData = [];
        $existingUsersData = [];

        // Filter users and licenses
        foreach ($lics as $lic){
            $user = $lic->user;
            // if new user
            if (in_array($user->id, $newUsersIds)){
                $newUsers[$lic->license_func_type_id][$lic->license_type_id][] = $lic->user;
            } else { // if existing user

                if (!isset($existingUsersData[$lic->license_func_type_id][$lic->license_type_id])) {
                    $existingUsersData[$lic->license_func_type_id][$lic->license_type_id]['count'] = 0;
                }

                $existingUsersData[$lic->license_func_type_id][$lic->license_type_id]['count']++;
                $existingUsersData[$lic->license_func_type_id][$lic->license_type_id]['users'][] = $user->getUserObj();
            }
        }

        // Get statistics for new users
        foreach($newUsers as $licFuncTypeId => $g){
            foreach ($g as $licTypeId => $gg){
                $newUsersData[$licFuncTypeId][$licTypeId] = $this->statsForUsers($gg);
            }
        }

        return [$existingUsersData, $newUsersData];
    }

    private function statsForUsers($users)
    {
        $info = new stdClass();
        $info->totalCount = count($users);

        // Count platforms
        $countries = [];
        $platforms = [];
        $neverLoggedIn['count'] = 0;
        foreach($users as $user){
            $userObj = $user->getUserObj();

            if (!$user->subscriber || $user->subscriber->date_first_login){

                $key1 = $user->subscriber->location['country'];
                if (!$key1){
                    $key1= "unknown";
                }

                if (!array_key_exists($key1, $countries)) {
                    $countries[$key1]['count'] = 0;
                }
                $countries[$key1]['count']++;
                $countries[$key1]['users'][] = $userObj;

                arsort($countries);

                if ($user->subscriber->app_version){
                    $key2 = $user->subscriber->app_version_obj->platformDesc();
                    if (!array_key_exists($key2, $platforms)){
                        $platforms[$key2]['count'] = 0;
                    }

                    $platforms[$key2]['count']++;
                    $platforms[$key2]['users'][] = $userObj;
                }

                arsort($platforms);

            } else {
                $neverLoggedIn['count']++;
                $neverLoggedIn['users'][] = $userObj;
            }
        }

        $info->countries = $countries;
        $info->platforms = $platforms;
        $info->neverLoggedIn = $neverLoggedIn;
        return $info;
    }
}