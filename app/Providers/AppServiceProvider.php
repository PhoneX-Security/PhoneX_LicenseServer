<?php namespace Phonex\Providers;

use Illuminate\Support\ServiceProvider;
use Phonex\Utils\DateRangeValidator;
use Phonex\Utils\Stats;
use Validator;

class AppServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap any application services.
	 *
	 */
	public function boot()
	{
		// provide a new custom validator
		Validator::extend('date_range',  DateRangeValidator::class . '@validate');
	}

	/**
	 * Register any application services.
	 *
	 * This service provider is a great spot to register your various container
	 * bindings with the application. As you can see, we are registering our
	 * "Registrar" implementation here. You can add your own bindings too!
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind(
			'Illuminate\Contracts\Auth\Registrar',
			'Phonex\Services\Registrar'
		);

		$this->app->singleton(Stats::class, function(){
			return new Stats();
		});
	}

}
