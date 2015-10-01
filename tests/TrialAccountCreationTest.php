<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Phonex\Http\Controllers\AccountController;
use Phonex\License;
use Phonex\Model\NotificationType;
use Phonex\Model\SupportNotification;
use Phonex\Subscriber;
use Phonex\TrialRequest;
use Phonex\User;

class TrialAccountCreationTest extends TestCase {
    // this wraps all tests in a transaction
    use DatabaseTransactions;

    const URL = '/account/trial';
    const TEST_USERNAME = "qtraaabbccd";

    public function setUp(){
        // has to do this here before the framework is started because phpunit prints something before headers are sent
        @session_start();
        parent::setUp();
    }

	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function testMissingFields(){
		$response = $this->call('POST', self::URL);
   		$this->assertEquals(200, $response->getStatusCode());
        $json = json_decode($response->getContent());
        $this->assertEquals(AccountController::RESP_ERR_INVALID_REQUEST, $json->responseCode);

        $response = $this->call('POST', self::URL, ['version' => 1, 'captcha' => 1]);
        $json = json_decode($response->getContent());
        $this->assertEquals(AccountController::RESP_ERR_INVALID_REQUEST, $json->responseCode);

        $response = $this->call('POST', self::URL, ['imei' => 1, 'captcha' => 1]);
        $json = json_decode($response->getContent());
        $this->assertEquals(AccountController::RESP_ERR_INVALID_REQUEST, $json->responseCode);
	}

    public function testInvalidVersion(){
        $this->callAndCheckResponse(self::URL,
            ['version' => AccountController::VERSION + 1, 'imei' => 'a', 'captcha' =>'captcha', 'username' => 'someusername'],
            AccountController::RESP_ERR_UNSUPPORTED_VERSION
        );
    }

    public function testBadCaptcha(){
        $this->callAndCheckResponse(self::URL,
            ['version' => AccountController::VERSION, 'imei' => 'a', 'captcha' =>'bad_captcha', 'username' => 'someusername'],
            AccountController::RESP_ERR_BAD_CAPTCHA,
            AccountController::TEST_NON_QA_IP
        );
    }

    public function testBadUsername(){
        $this->callAndCheckResponse(self::URL,
            ['version' => AccountController::VERSION, 'imei' => 'a', 'captcha' =>'captcha', 'username' => '1'],
            AccountController::RESP_ERR_USERNAME_BAD_FORMAT
        );
    }
//
    public function testExistingUsername(){
        $this->callAndCheckResponse(self::URL,
            ['version' => AccountController::VERSION, 'imei' => 'a', 'captcha' =>'captcha', 'username' => 'test318'],
            AccountController::RESP_ERR_EXISTING_USERNAME
        );
    }

//    // Disabled - automatic trial name assigning is disabled
//    public function testTrialCreation(){
//        try {
//            $userCount = User::all()->count();
//            $licenseCount = License::all()->count();
//            $subscriberCount = Subscriber::all()->count();
//            $trialReqCount = TrialRequest::all()->count();
//
//            $response = $this->call('POST', self::URL, ['version' => AccountController::VERSION, 'imei' => 'a', 'captcha' =>'captcha']);
//            $json = json_decode($response->getContent());
////            var_dump($response);
//            $this->assertEquals(AccountController::RESP_OK, $json->responseCode);
//
//            // assert all records created
//            $this->assertEquals($userCount + 1, User::all()->count());
//            $this->assertEquals($licenseCount + 1, License::all()->count());
//            $this->assertEquals($subscriberCount + 1, Subscriber::all()->count());
//            $this->assertEquals($trialReqCount + 1, TrialRequest::all()->count());
//        } finally {
//            // Subscriber has to be deleted manually - tables use MyISAM engine and are on a different server, do not support transactions
//            $user = User::where('username', $json->username)->first();
//            if ($user){
//                $user->subscriber->deleteWithContactListRecords();
//            }
//        }
//    }

    public function testTrialCreationWithUsername(){
        try {
            $userCount = User::all()->count();
            $licenseCount = License::all()->count();
            $subscriberCount = Subscriber::all()->count();
            $trialReqCount = TrialRequest::all()->count();

            $json = $this->callAndCheckResponse(self::URL, [
                'version' => AccountController::VERSION,
                'imei' => 'abcdsejhhk000046666',
                'captcha' =>'captcha',
                'username' => self::TEST_USERNAME
            ], AccountController::RESP_OK);
            $this->assertEquals(self::TEST_USERNAME, $json->username);

            // check user has welcome message dispatched
            $user = User::where('username', $json->username)->first();
            $notification = SupportNotification::where(['user_id' => $user->id, 'notification_type_id' => NotificationType::getWelcomeNotification()->id])->first();
            $this->assertNotNull($notification);

            //
            // assert all records created
            $this->assertEquals($userCount + 1, User::all()->count());
            $this->assertEquals($licenseCount + 1, License::all()->count());
            $this->assertEquals($subscriberCount + 1, Subscriber::all()->count());
            $this->assertEquals($trialReqCount + 1, TrialRequest::all()->count());
        } catch (Exception $e){
          Log::error("error", [$e]);
        } finally {
            $user = User::where('username', $json->username)->first();
            if ($user){
                $user->subscriber->deleteWithContactListRecords();
            }
        }
    }

    public function testImeiBlockation(){
        try {
            $imei = "360a2d6b3383d73581d7a1a9";

            $usernames = [];

            for($i=1; $i<=3; $i++){
                $expectedResponse = AccountController::RESP_OK;
                if ($i >= 3){
                    // third user should not be created (because second has successfully logged in)
                    $expectedResponse = AccountController::RESP_ERR_TRIAL_EXISTS;
                }

                $json = $this->callAndCheckResponse(self::URL, [
                    'version' => AccountController::VERSION,
                    'imei' => $imei,
                    'captcha' =>'captcha',
                    'username' => self::TEST_USERNAME . $i
                ], $expectedResponse, null, "Trying to create user " . self::TEST_USERNAME . $i);

                // fake second user has logged in
                if ($i == 2){
                    $user = User::where('username', $json->username)->first();
                    $user->subscriber->date_first_login = Carbon::now();
                    $user->subscriber->save();
                }

                if ($expectedResponse == AccountController::RESP_OK){
                    $this->assertEquals(self::TEST_USERNAME . $i, $json->username);
                }

                $usernames[] = self::TEST_USERNAME . $i;
            }
        } finally {
            foreach($usernames as $username){
                $user = User::where('username', $username)->first();
                if ($user){
                    $user->subscriber->deleteWithContactListRecords();
                }
            }
        }
    }
}