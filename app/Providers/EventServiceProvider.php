<?php namespace Phonex\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;


class EventServiceProvider extends ServiceProvider {

	/**
	 * The event handler mappings for the application.
	 *
	 * @var array
	 */
	protected $listen = [
		'event.name' => ['EventListener'],
        'Phonex\Events\AuditEvent' => ['Phonex\Handlers\Events\AuditEventHandler@onEventReceived'],
	];

	/**
	 * Register any other events for your application.
	 *
	 * @param  \Illuminate\Contracts\Events\Dispatcher  $events
	 * @return void
	 */
	public function boot(DispatcherContract $events)
	{
		parent::boot($events);
//        Event::listen('Phonex\Events\AuditEvent', 'Phonex\Handlers\Events\AuditEventHandler@audit');
	}

}
