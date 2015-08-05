<?php namespace Phonex\Http\Controllers\Api;

use Phonex\Http\Controllers\Controller;
use Phonex\Http\Requests;

class SupportNotificationsController extends Controller {

	public function __construct(){
	}

    public function batch()
    {
        // just example by now
        $batch = new \stdClass();
        $batch->batch_id = 5734;
        $batch->data = [];

        $obj1 = new \stdClass();
        $obj1->type = 'welcome-message';
        $obj1->locale = 'en';
        $obj1->sips = ["adevel624@phone-x.net", "test318@phone-x.net"];
        $obj1->message = "Test message for adevel624 and test318. Welcome to PhoneX!";
        $batch->data[] = $obj1;

        $obj2 = new \stdClass();
        $obj2->locale = 'cs';
        $obj1->type = 'license-expires-in-day';
        $obj2->sips = ["test322@phone-x.net"];
        $obj2->message = "Testovací zpráva pro test322 - za den ti expiruje licence kreténe, kup si novou.";
        $batch->data[] = $obj2;

        return json_encode($batch);
    }
}
