<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Phonex\Http\Controllers\AccountController;
use Phonex\Model\NotificationType;
use Phonex\Subscriber;
use Phonex\User;

class SupportNotificationApiTest extends TestCase {
    // this wraps all tests in a transaction
    use DatabaseTransactions;

    const URL = '/api/support-notifications/batch';
    const TEST_USERNAME = "jsonbstuser20";

    const WELCOME_TEXT_EN = "WELCOME DEAR STRANGER!";
    const APP_VERSION_EN = '{"v":1,"dev":"samsung;jflte;GT-I9505","locales":["en_US"],"p":"android","ac":"1.4.2-Alpha","pid":19,"rc":2293,"info":"PhoneX"}';
    const APP_VERSION_NON_EXISTING_LOCALE = '{"v":1,"dev":"samsung;jflte;GT-I9505","locales":["nonexisting"],"p":"android","ac":"1.4.2-Alpha","pid":19,"rc":2293,"info":"PhoneX"}';

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
                'imei' => 'abcde',
                'captcha' =>'captcha',
                'username' => self::TEST_USERNAME
            ], AccountController::RESP_OK);

            // save translation for english locale of welcome message
            $welcomeNotificationType = NotificationType::getWelcomeNotification();
            $welcomeNotificationType->translate('en')->text = self::WELCOME_TEXT_EN;
            $welcomeNotificationType->save();

//            // check that when user has empty app_version, notification message is not contained in the batch (he has to log in first)
            $response = $this->call('GET', self::URL, ['k' => 'ovual3ohshiChai5EiPeeP4ma']);
            $json = json_decode($response->getContent());
            $this->assertEquals(0, count($json->notifications));

            // update app_version locale for given subscriber - simulate he has logged in
            Subscriber::where('username', $userJson->username)->update(['app_version' => self::APP_VERSION_EN]);

            // check that support notification batch contains given user
            $response = $this->call('GET', self::URL, ['k' => 'ovual3ohshiChai5EiPeeP4ma']);
            $json = json_decode($response->getContent());

            $this->assertEquals(1, count($json->notifications));
            $this->assertEquals($userJson->username . "@phone-x.net", $json->notifications[0]->sip);
            $this->assertEquals(NotificationType::TYPE_WELCOME_MESSAGE, $json->notifications[0]->type);
            $this->assertEquals(self::WELCOME_TEXT_EN, $json->notifications[0]->text);

            // check that when called twice, user is not contained in the batch
            $response = $this->call('GET', self::URL, ['k' => 'ovual3ohshiChai5EiPeeP4ma']);
            $json = json_decode($response->getContent());
            $this->assertEquals(0, count($json->notifications));
        } finally {
            $user = User::where('username', $userJson->username)->first();
            if ($user){
                $user->subscriber->deleteWithContactListRecords();
            }
        }
    }

    public function testNonExistingLocale(){
        try {
            // create trial account
            $userJson = $this->callAndCheckResponse(TrialAccountCreationTest::URL, [
                'version' => AccountController::VERSION,
                'imei' => 'abcde',
                'captcha' =>'captcha',
                'username' => self::TEST_USERNAME
            ], AccountController::RESP_OK);

            // save translation for english locale of welcome message
            $welcomeNotificationType = NotificationType::getWelcomeNotification();
            $welcomeNotificationType->translate('en')->text = self::WELCOME_TEXT_EN;
            $welcomeNotificationType->save();

            // Simulate login with non existing locale
            Subscriber::where('username', $userJson->username)->update(['app_version' => self::APP_VERSION_NON_EXISTING_LOCALE]);

            // check that support notification batch contains given user
            $response = $this->call('GET', self::URL, ['k' => 'ovual3ohshiChai5EiPeeP4ma']);
            $json = json_decode($response->getContent());

            $this->assertEquals(1, count($json->notifications));
            $this->assertEquals($userJson->username . "@phone-x.net", $json->notifications[0]->sip);
            // check text is in english as default locale
            $this->assertEquals(self::WELCOME_TEXT_EN, $json->notifications[0]->text);
        } finally {
            $user = User::where('username', $userJson->username)->first();
            if ($user){
                $user->subscriber->deleteWithContactListRecords();
            }
        }
    }
}