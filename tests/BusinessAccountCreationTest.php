<?php

use Phonex\Commands\CreateUserWithLicense;
use Phonex\Http\Controllers\AccountController;
use Phonex\License;
use Phonex\LicenseType;
use Phonex\Subscriber;
use Phonex\TrialRequest;
use Phonex\User;

class BusinessAccountCreationTest extends TestCase {
    const URL = '/account/business-account';
    const TEST_USERNAME = "kexo_test123_business";

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
	}

    public function testBadCaptcha(){
        $this->callAndCheckResponse(
            ['version' => AccountController::VERSION, 'imei' => 'a', 'captcha' =>'bad_captcha', 'username' => '1', 'bcode' => 1],
            AccountController::RESP_ERR_BAD_CAPTCHA
        );
    }

    public function testBadUsername(){
        $this->callAndCheckResponse(
            ['version' => AccountController::VERSION, 'imei' => 'a', 'captcha' =>'captcha', 'username' => '1', 'bcode' => 1],
            AccountController::RESP_ERR_USERNAME_BAD_FORMAT
        );
    }
//
    public function testExistingUsername(){
        $this->callAndCheckResponse(
            ['version' => AccountController::VERSION, 'imei' => 'a', 'captcha' =>'captcha', 'username' => 'test318', 'bcode' => 1],
            AccountController::RESP_ERR_EXISTING_USERNAME
        );
    }

    public function testBadBusinessCode(){
        $nonExistingUsername = "kajsmentke";

        // first delete user if exists
        $oldUser = User::where('username', $nonExistingUsername)->first();
        if ($oldUser != null){
            $oldUser->deleteWithLicenses();
        }

        $params = [
            'version' => AccountController::VERSION,
            'imei' => 'a',
            'captcha' =>'captcha',
            'username' => $nonExistingUsername,
            'bcode' => "too_short"
        ];
        $response = $this->callNonQa($params);

        $json = json_decode($response->getContent());

        $this->assertEquals(AccountController::RESP_ERR_BAD_BUSINESS_CODE, $json->responseCode);
    }

//    public function testKajsmentke(){
//        $licenseType = LicenseType::find(1);
//        $command = new CreateUserWithLicense("fasirka", "fasirka_heslo", $licenseType);
//
//        $r = Bus::dispatch($command);
//        dd($r);
//    }

    /**
     * Main test
     */
    public function testAccountCreation(){
        $username1 = "kajsmentke";
        $username2 = "kozmeker";

        // first delete users if exist
        $oldUser = User::where('username', $username1)->first();
        if ($oldUser != null){
            $oldUser->deleteWithLicenses();
        }
        $oldUser = User::where('username', $username2)->first();
        if ($oldUser != null){
            $oldUser->deleteWithLicenses();
        }

        // now create user1 and issue Mobil Pohotovost business code


//        $userCount = User::all()->count();
//        $licenseCount = License::all()->count();
//        $subscriberCount = Subscriber::all()->count();
//        $trialReqCount = TrialRequest::all()->count();
//
//        $response = $this->call(
//            'POST',
//            self::URL,
//            [
//                'version' => AccountController::VERSION,
//                'imei' => 'a',
//                'captcha' =>'captcha',
//                'username' => self::TEST_USERNAME
//            ]
//            , [], [], ['REMOTE_ADDR' => AccountController::TEST_NON_QA_IP]
//        );
//        $json = json_decode($response->getContent());
//
//        $this->assertEquals(AccountController::RESP_OK, $json->responseCode);
//        $this->assertEquals(self::TEST_USERNAME, $json->username);
//
//        // assert all records created
//        $this->assertEquals($userCount + 1, User::all()->count());
//        $this->assertEquals($licenseCount + 1, License::all()->count());
//        $this->assertEquals($subscriberCount + 1, Subscriber::all()->count());
//        $this->assertEquals($trialReqCount + 1, TrialRequest::all()->count());
//
//        // delete all
//        $user = User::where('username', $json->username)->first();
//        $user->deleteWithLicenses();
//
//        // assert again
//        $this->assertEquals($userCount, User::all()->count());
//        $this->assertEquals($licenseCount, License::all()->count());
//        $this->assertEquals($subscriberCount, Subscriber::all()->count());
    }

    /* Helper functions */
    private function callAndCheckResponse(array $params, $expectedJsonCode){
        $response = $this->call('POST', self::URL, $params);
        $json = json_decode($response->getContent());
        $this->assertEquals($expectedJsonCode, $json->responseCode);
    }

    private function callNonQa(array $params){
        return $this->call(
            'POST',
            self::URL, $params,
            [], [], ['REMOTE_ADDR' => AccountController::TEST_NON_QA_IP]
        );
    }
}