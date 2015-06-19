<?php namespace Phonex\Http\Middleware;

use Closure;

/**
 * Checks for 'route', <route_name> permission for current user using Lock
 * Class AclRoute
 * @package Phonex\Http\Middleware
 */
class AclRoute{

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
        $lockResources = $this->getAclResources($request);

        $u = \Auth::getUser();
        if ($lockResources && !$u->can('route', $lockResources)){
            // TODO log it here
            return response('Unauthorized. Your actions are audited.', 401);
        }
		return $next($request);
	}

    private function getAclResources($request)
    {
        $actions = $request->route()->getAction();
        return array_key_exists('acl-resource', $actions)
            ? $actions['acl-resource']
            : [];
    }
}
