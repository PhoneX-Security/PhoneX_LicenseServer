<?php

use Phonex\Http\Controllers\AccountController;
use Phonex\License;
use Phonex\Subscriber;
use Phonex\TrialRequest;
use Phonex\User;

class TrialAccountCreationTest extends TestCase {
    const URL = '/account/trial';

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
        $this->callAndCheckResponse(
            ['version' => AccountController::VERSION + 1, 'imei' => 'a', 'captcha' =>'captcha'],
            AccountController::RESP_ERR_UNSUPPORTED_VERSION
        );
    }

    public function testBadCaptcha(){
        $this->callAndCheckResponse(
            ['version' => AccountController::VERSION, 'imei' => 'a', 'captcha' =>'bad_captcha'],
            AccountController::RESP_ERR_BAD_CAPTCHA
        );
    }

    public function testBadUsername(){
        $this->callAndCheckResponse(
            ['version' => AccountController::VERSION, 'imei' => 'a', 'captcha' =>'captcha', 'username' => '1'],
            AccountController::RESP_ERR_USERNAME_BAD_FORMAT
        );
    }
//
    public function testExistingUsername(){
        $this->callAndCheckResponse(
            ['version' => AccountController::VERSION, 'imei' => 'a', 'captcha' =>'captcha', 'username' => 'test318'],
            AccountController::RESP_ERR_EXISTING_USERNAME
        );
    }
    /* Helper functions */
    private function callAndCheckResponse(array $params, $expectedJsonCode){
        $response = $this->call('POST', self::URL, $params);
        $json = json_decode($response->getContent());
        $this->assertEquals($expectedJsonCode, $json->responseCode);
    }

    // continue with normal tests
    public function testTrialCreation(){
        $userCount = User::all()->count();
        $licenseCount = License::all()->count();
        $subscriberCount = Subscriber::all()->count();
        $trialReqCount = TrialRequest::all()->count();

        $response = $this->call('POST', self::URL, ['version' => AccountController::VERSION, 'imei' => 'a', 'captcha' =>'captcha']);
        $json = json_decode($response->getContent());
        $this->assertEquals(AccountController::RESP_OK, $json->responseCode);

        // assert all records created
        $this->assertEquals($userCount + 1, User::all()->count());
        $this->assertEquals($licenseCount + 1, License::all()->count());
        $this->assertEquals($subscriberCount + 1, Subscriber::all()->count());
        $this->assertEquals($trialReqCount + 1, TrialRequest::all()->count());
    }





}