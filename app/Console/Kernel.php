<?php namespace Phonex\Console;

use Bus;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Phonex\Commands\RefreshSubscribers;

class Kernel extends ConsoleKernel {

	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		'Phonex\Console\Commands\Inspire',
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule){
        // Refresh subscriber table every day
        $schedule->call(function(){
            Bus::dispatch(new RefreshSubscribers());
        })->dailyAt('23:59');

	}

}
