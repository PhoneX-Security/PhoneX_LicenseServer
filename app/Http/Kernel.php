<?php namespace Phonex\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel {

	/**
	 * The application's global HTTP middleware stack.
	 *
	 * @var array
	 */
	protected $middleware = [
		'Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode',
		'Illuminate\Cookie\Middleware\EncryptCookies',
		'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
		'Illuminate\Session\Middleware\StartSession',
		'Illuminate\View\Middleware\ShareErrorsFromSession',
        'BeatSwitch\Lock\Integrations\Laravel\Middleware\LockPermissions'
//		 TODO allow this
//		'Phonex\Http\Middleware\CustomVerifyCsrfToken',
	];

	/**
	 * The application's route middleware.
	 *
	 * @var array
	 */
	protected $routeMiddleware = [
		'auth' => 'Phonex\Http\Middleware\Authenticate',
		'auth.basic' => 'Illuminate\Auth\Middleware\AuthenticateWithBasicAuth',
		'auth.client_cert' => 'Phonex\Http\Middleware\ClientCertAuth',
		'guest' => 'Phonex\Http\Middleware\RedirectIfAuthenticated',
        'acl' => 'Phonex\Http\Middleware\AclRoute',
	];

}
