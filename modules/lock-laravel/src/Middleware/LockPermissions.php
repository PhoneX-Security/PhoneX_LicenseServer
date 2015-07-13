<?php
namespace BeatSwitch\Lock\Integrations\Laravel\Middleware;

use BeatSwitch\Lock\Manager;
use BeatSwitch\Lock\Lock;
use Closure;
use Illuminate\Config\Repository;

class LockPermissions {
    /**
     * @var Repository
     */
    private $config;
    /**
     * @var Manager
     */
    private $lockManager;
    /**
     * @var Lock
     */
    private $lock;

    function __construct(Repository $config, Manager $lockManager, Lock $lock)
    {
        $this->config = $config;
        $this->lockManager = $lockManager;
        $this->lock = $lock;
    }

    /**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
        // Load all Lock permissions for the current user
        $this->bootstrapPermissions();
		return $next($request);
	}

    /**
     * Here we should execute the permissions callback from the config file so all
     * the roles and aliases get registered and if we're using the array driver,
     * all of our permissions get set beforehand.
     */
    protected function bootstrapPermissions()
    {
        // Get the permissions callback from the config file.
        $callback = $this->config->get('lock.permissions', null);

        // Add the permissions which were set in the config file.
        if (! is_null($callback)) {
            call_user_func($callback, $this->lockManager, $this->lock);
        }
    }
}
