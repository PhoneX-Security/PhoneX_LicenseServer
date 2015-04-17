<?php namespace Phonex\Handlers\Events;

use Illuminate\Contracts\Queue\ShouldBeQueued;
use Illuminate\Support\Facades\Auth;
use Log;
use Phonex\AuditTrail;
use Phonex\Events\AuditEvent;

class AuditEventHandler {

	public function __construct(){
	}

    public function onEventReceived(AuditEvent $event){
        if (Auth::guest()){
            Log::warning("AuditEventHandler; onEventReceived - cannot log because user is not logged in. This should not happen! ");
            return;
        }

        $auditTrial = AuditTrail::fillFrom($event);
        $auditTrial->save();
    }
}
