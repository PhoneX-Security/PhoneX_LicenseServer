<?php namespace Phonex\Handlers\Events;

use Illuminate\Support\Facades\Auth;
use Phonex\AuditTrail;
use Phonex\Events\AuditEvent;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use Phonex\Exceptions\UserLoggedOutException;

class AuditEventHandler {

	public function __construct(){
	}

    public function onEventReceived(AuditEvent $event){
        if (Auth::guest()){
            throw new UserLoggedOutException("Cannot audit received event, user is logged out.");
        }

        $auditTrial = AuditTrail::fillFrom($event);
        $auditTrial->save();
    }
}
