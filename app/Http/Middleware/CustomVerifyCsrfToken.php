<?php namespace Phonex\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

class CustomVerifyCsrfToken extends VerifyCsrfToken {

    protected $excludedRoutes = ['adminer'];

	public function handle($request, Closure $next)
	{
        if ($this->isExcludedRoute($request)){
            return $next($request);
        } else {
            return parent::handle($request, $next);
        }
	}

    private function isExcludedRoute($request)
    {
        if (count($request->segments()) > 0
            && in_array($request->segment(1), $this->excludedRoutes)){
            return true;
        } else {
            return false;
        }
    }
}
