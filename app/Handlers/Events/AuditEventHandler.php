<?php namespace Phonex\Handlers\Events;

use App;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use Illuminate\Support\Facades\Auth;
use Phonex\AuditTrail;
use Phonex\Events\AuditEvent;
use Phonex\Exceptions\UserLoggedOutException;

class AuditEventHandler {

	public function __construct(){
	}

    public function onEventReceived(AuditEvent $event){
        if (App::runningUnitTests()){
            // no need to audit
            return;
        }

        if (Auth::guest()){
            throw new UserLoggedOutException("Cannot audit received event, user is logged out.");
        }

        $auditTrial = AuditTrail::fillFrom($event);
        $auditTrial->save();
    }
}
