<?php namespace Phonex\Http\Middleware;

use App;
use Closure;
use Log;
use Phonex\Subscriber;
use Phonex\User;
use Phonex\Utils\ClientCertData;
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

		// common name of certificate is user's sip (e.g. test318@phone-x.net)
		// it will be checked against opensips subscriber table
		$clientCertVerify = Request::server('SSL_CLIENT_VERIFY', 'NONE');
		$clientCommonName = Request::server('SSL_CLIENT_S_DN_CN');
		if (!Request::secure() || $clientCertVerify !== 'SUCCESS' || !$clientCommonName){
			abort(401);
		}

//		dd($clientCommonName);

		$clientCertData = null;
		try{
			$clientCertData = ClientCertData::parseFromRequest($request);
		} catch (\Exception $e){
			// non-email format, this should never happen
			abort(400);
		}

		$user = User::where(['email' => $clientCertData->sip])->first();
//		$subscriberCount = Subscriber::where(['username' => $clientCertData->username, 'domain'=> $clientCertData->domain])->count();
//		if($subscriberCount < 1){
		if (!$user){
			Log::warning("ClientCertAuth; user tried to log in, access was denied, no such subscriber email in DB", [$clientCommonName]);
			abort(401);
		}
		$request->attributes->add([MiddlewareAttributes::CLIENT_CERT_AUTH_USER => $user]);
		return $next($request);
	}
}
