<?php namespace Phonex\Utils;


use Carbon\Carbon;
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
        $timePeriod = null;
        $dateFrom = null;
        $dateTo = null;

        if ($periodType === self::WEEK){
            $timePeriod = 7*24*60*60;
            $dateFrom = Carbon::now()->subWeeks($totalPeriods);
            $dateTo = Carbon::now()->subWeeks($totalPeriods - 1);
        } else if ($periodType === self::DAY){
            $timePeriod = 24*60*60;
            $dateFrom = Carbon::now()->subDays($totalPeriods);
            $dateTo = Carbon::now()->subDays($totalPeriods - 1);
        } else {
            throw new \Exception("unknown period type");
        }


        // week variant
        $labels = [];
        for($i=0; $i<$totalPeriods; $i++){
            $labels[] = $dateFrom->day . "." . $dateFrom->month . ". - " . $dateTo->day . "." . $dateTo->month;

            $dateFrom = $dateFrom->addWeek();
            $dateTo = $dateTo->addWeek();
        }
        return $labels;
    }

}