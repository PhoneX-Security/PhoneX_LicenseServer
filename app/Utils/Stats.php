<?php namespace Phonex\Utils;


use Carbon\Carbon;
use Phonex\Model\UsageLogs;
use Phonex\User;

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

}