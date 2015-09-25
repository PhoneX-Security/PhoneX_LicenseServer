<?php namespace Phonex\Http\Middleware;

use App;
use Closure;
use Log;
use Phonex\Subscriber;
use Request;

/**
 * Verifies client cert authentication according to $_SERVER variables
 * Apache should be configured to check for this (in production mode)
 */
class ClientCertAuth{

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		// check if local environment
		// if so, skip client cert authentication
		if (App::isLocal() || App::environment() === 'testing'){
			return $next($request);
		}

		// common name of certificate be user's sip (e.g. test318@phone-x.net)
		// it will be checked against opensips subscriber table
		$clientCertVerify = Request::server('SSL_CLIENT_VERIFY', 'NONE');
		$clientCommonName = Request::server('SSL_CLIENT_S_DN_CN');
		if (!Request::secure() || $clientCertVerify !== 'SUCCESS' || !$clientCommonName){
			abort(401);
		}

		if(!filter_var($clientCommonName, FILTER_VALIDATE_EMAIL)) {
			// non-email format, this should never happen
			abort(400);
		}

		$sipParams = explode('@', $clientCommonName);
		$username = $sipParams[0];
		$domain = $sipParams[1];

		$subscriberCount = Subscriber::where(['username' => $username, 'domain'=> $domain])->count();
		if($subscriberCount < 1){
			Log::warning("ClientCertAuth; user tried to log in, access was denied, no such subscriber email in DB", [$clientCommonName]);
			abort(401);
		}

		return $next($request);
	}
}
