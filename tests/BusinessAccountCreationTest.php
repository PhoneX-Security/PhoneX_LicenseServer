<?php

use Phonex\Commands\CreateBusinessCodePair;
use Phonex\Commands\CreateUserWithLicense;
use Phonex\Group;
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

    /**
     * Main test
     */
    public function testAccountCreation(){
        $username1 = "kajsmentke";
        $username2 = "kozmeker";
        $username3 = "fajnsmeker";

        // first delete users if exist
        $this->deleteUsers([$username1, $username2, $username3]);

        // now create user1 and issue Mobil Pohotovost business code
        $trialLic = LicenseType::find(1);
        $command = new CreateUserWithLicense($username1, "fasirka_heslo", $trialLic);
        $user1 = Bus::dispatch($command);

        // predefined group + license type
        $mpGroup = Group::where('name', 'Mobil Pohotovost')->first();
        $mpLicenseType = LicenseType::where('name', 'mp_half_year')->first();

        $command = new CreateBusinessCodePair($user1, $mpLicenseType, $mpGroup);
        $codePair = Bus::dispatch($command);

        // remember counts
        $userCount = User::all()->count();
        $licenseCount = License::all()->count();
        $subscriberCount = Subscriber::all()->count();
        // now use first code to get a license
        $response1 = $this->call(
            'POST',
            self::URL,
            [
                'version' => AccountController::VERSION,
                'imei' => 'a',
                'captcha' =>'captcha',
                'username' => $username2,
                'bcode' => $codePair[0]->code
            ]
            , [], [], ['REMOTE_ADDR' => AccountController::TEST_NON_QA_IP]
        );

        $json = json_decode($response1->getContent());
        $this->assertEquals(AccountController::RESP_OK, $json->responseCode);
        $this->assertEquals($username2, $json->username);

        // assert all records created
        $this->assertEquals($userCount + 1, User::all()->count());
        $this->assertEquals($licenseCount + 1, License::all()->count());
        $this->assertEquals($subscriberCount + 1, Subscriber::all()->count());

        // check we cannot create another license on the same business code
        $responseX = $this->call(
            'POST',
            self::URL,
            [
                'version' => AccountController::VERSION,
                'imei' => 'a',
                'captcha' =>'captcha',
                'username' => $username3,
                'bcode' => $codePair[0]->code
            ]
            , [], [], ['REMOTE_ADDR' => AccountController::TEST_NON_QA_IP]
        );

        $json = json_decode($responseX->getContent());
        $this->assertEquals(AccountController::RESP_ERR_ALREADY_USED_BUSINESS_CODE, $json->responseCode);



        // now use the second code to get a license
//        $response2 = $this->call(
//            'POST',
//            self::URL,
//            [
//                'version' => AccountController::VERSION,
//                'imei' => 'a',
//                'captcha' =>'captcha',
//                'username' => $username3,
//                'bcode' => $codePair[1]->code
//            ]
//            , [], [], ['REMOTE_ADDR' => AccountController::TEST_NON_QA_IP]
//        );
//        $json = json_decode($response2->getContent());//
//        $this->assertEquals(AccountController::RESP_OK, $json->responseCode);
//        $this->assertEquals(self::TEST_USERNAME, $username3);
//
//        // assert all records created
//        $this->assertEquals($userCount + 2, User::all()->count());
//        $this->assertEquals($licenseCount + 2, License::all()->count());
//        $this->assertEquals($subscriberCount + 2, Subscriber::all()->count());

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

    private function deleteUsers(array $usernames){
        foreach ($usernames as $username){
            $oldUser = User::where('username', $username)->first();
            if ($oldUser != null){
                $oldUser->deleteWithLicenses();
            }
        }
    }

    private function callNonQa(array $params){
        return $this->call(
            'POST',
            self::URL, $params,
            [], [], ['REMOTE_ADDR' => AccountController::TEST_NON_QA_IP]
        );
    }
}