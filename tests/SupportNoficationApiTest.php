<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Phonex\Http\Controllers\AccountController;
use Phonex\Model\NotificationType;
use Phonex\User;

class SupportNotificationApiTest extends TestCase {
    // this wraps all tests in a transaction
    use DatabaseTransactions;

    const URL = '/api/support-messaging/batch';
    const TEST_USERNAME = "jsonbstuser12";

    public function setUp(){
        // has to do this here before the framework is started because phpunit prints something before headers are sent
        @session_start();
        parent::setUp();
    }

    public function testWelcomeMessage(){
        try {
            // create trial account
            $userJson = $this->callAndCheckResponse(TrialAccountCreationTest::URL, [
                'version' => AccountController::VERSION,
                'imei' => 'a',
                'captcha' =>'captcha',
                'username' => self::TEST_USERNAME
            ], AccountController::RESP_OK);

            // check that support notification batch contains given user
            $response = $this->call('GET', self::URL);
            $json = json_decode($response->getContent());

            $this->assertEquals(1, count($json->notifications));
            $this->assertEquals($userJson->username . "@phone-x.net", $json->notifications[0]->sip);
            $this->assertEquals(NotificationType::TYPE_WELCOME_MESSAGE, $json->notifications[0]->type);

            $this->assertEquals(NotificationType::TYPE_WELCOME_MESSAGE, $json->notification_types[0]->type);

            // check that when called twice, user is not contained in the batch
            $response = $this->call('GET', self::URL);
            $json = json_decode($response->getContent());
            $this->assertEquals(0, count($json->notifications));
            $this->assertEquals(0, count($json->notification_types));
        } finally {
            $user = User::where('username', $userJson->username)->first();
            if ($user){
                $user->subscriber->deleteWithContactListRecords();
            }
        }
    }
}