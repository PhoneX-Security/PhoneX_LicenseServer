<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Phonex\Jobs\CreateBusinessCodePair;
use Phonex\Jobs\CreateSubscriberWithLicense;
use Phonex\Jobs\CreateUser;
use Phonex\Group;
use Phonex\Http\Controllers\AccountController;
use Phonex\License;
use Phonex\LicenseFuncType;
use Phonex\LicenseType;
use Phonex\Subscriber;
use Phonex\User;

class BusinessAccountCreationTest extends TestCase {
    use DatabaseTransactions;

    const URL = '/account/business-account';
    const TEST_USERNAME = "kajsmentke_sk";
    const TEST_USERNAME2 = "kozmeker_sk";
    const TEST_USERNAME3 = "fajnsmeker_sk";


    public function setUp(){
        // has to do this here before the framework is started because phpunit prints something before headers are sent
        @session_start();
        parent::setUp();

        // login as random user
        $user = User::find(1);
        $this->be($user);
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
        $this->callAndCheckResponse(self::URL,
            ['version' => AccountController::VERSION, 'imei' => 'a', 'captcha' =>'bad_captcha', 'username' => '1', 'bcode' => 1],
            AccountController::RESP_ERR_BAD_CAPTCHA,
            AccountController::TEST_NON_QA_IP
        );
    }

    public function testBadUsername(){
        $this->callAndCheckResponse(self::URL,
            ['version' => AccountController::VERSION, 'imei' => 'a', 'captcha' =>'captcha', 'username' => '1', 'bcode' => 1],
            AccountController::RESP_ERR_USERNAME_BAD_FORMAT
        );
    }
//
    public function testExistingUsername(){
        $this->callAndCheckResponse(self::URL,
            ['version' => AccountController::VERSION, 'imei' => 'a', 'captcha' =>'captcha', 'username' => 'test318', 'bcode' => 1],
            AccountController::RESP_ERR_EXISTING_USERNAME
        );
    }

    public function testBadBusinessCode(){
        $nonExistingUsername = "kajsmentkeunique";

        $this->callAndCheckResponse(self::URL, [
            'version' => AccountController::VERSION,
            'imei' => 'a',
            'captcha' =>'captcha',
            'username' => $nonExistingUsername,
            'bcode' => "too_short"
        ], AccountController::RESP_ERR_BAD_BUSINESS_CODE);
    }

    /**
     * Main test
     */
    public function testAccountCreation(){
        // Mock Queue push because it might trigger Stack Overflow
        $this->mockQueuePush();

        try {
            $username1 = self::TEST_USERNAME;
            $username2 = self::TEST_USERNAME2;
            $username3 = self::TEST_USERNAME3;

            // now create user1 and generate code pair
            $trialLic = LicenseType::find(1);
            $trialLicFunc = \Phonex\LicenseFuncType::getTrial();
            $command = new CreateUser($username1);
            $user1 = Bus::dispatch($command);
            $commandSub = new CreateSubscriberWithLicense($user1, $trialLic, $trialLicFunc, 'fasirka_heslo');
            Bus::dispatch($commandSub);

            // predefined group + license type
            $mpGroup = Group::where('name', 'Mobil Pohotovost')->first();
            $mpLicenseType = LicenseType::where('name', 'half_year')->first();
            $mpLicenseFuncType = LicenseFuncType::getFull();

            $command = new CreateBusinessCodePair($user1, $mpLicenseType, $mpLicenseFuncType);
            $command->addGroup($mpGroup);
            $codePair = Bus::dispatch($command);

            // remember counts
            $userCount = User::all()->count();
            $licenseCount = License::all()->count();
            $subscriberCount = Subscriber::all()->count();

            // now use first code to get a license
            $json = $this->callAndCheckResponse(self::URL,[
                'version' => AccountController::VERSION,
                'imei' => 'a',
                'captcha' =>'captcha',
                'username' => $username2,
                'bcode' => $codePair[0]->code
            ], AccountController::RESP_OK);
            $this->assertEquals($username2, $json->username);

            // assert all records created
            $this->assertEquals($userCount + 1, User::all()->count());
            $this->assertEquals($licenseCount + 1, License::all()->count());
            $this->assertEquals($subscriberCount + 1, Subscriber::all()->count());

            // check we cannot create another license on the same business code
            $json = $this->callAndCheckResponse(self::URL, [
                'version' => AccountController::VERSION,
                'imei' => 'a',
                'captcha' =>'captcha',
                'username' => $username3,
                'bcode' => $codePair[0]->code
            ], AccountController::RESP_ERR_ALREADY_USED_BUSINESS_CODE);

            // now use the second code to get a license
            $json = $this->callAndCheckResponse(self::URL, [
                'version' => AccountController::VERSION,
                'imei' => 'a',
                'captcha' =>'captcha',
                'username' => $username3,
                'bcode' => $codePair[1]->code
            ], AccountController::RESP_OK);
            $this->assertEquals($username3, $json->username);

            // expect two users in contact list (support and another business user)
            $user3 = User::where('username', $username3)->first();
            $this->assertEquals(2, count($user3->subscriber->subscribersInContactList));

//        // assert all records created
            $this->assertEquals($userCount + 2, User::all()->count());
            $this->assertEquals($licenseCount + 2, License::all()->count());
            $this->assertEquals($subscriberCount + 2, Subscriber::all()->count());

        } finally {
            // Subscribers table do not support transactions, delete manually
            $this->deleteSubscribers([$username1, $username2, $username3]);
        }
    }

    public function testAccountCreationWithParent(){
        // Mock Queue push because it might trigger Stack Overflow
        $this->mockQueuePush();

        try {
            $username1 = self::TEST_USERNAME;
            $username2 = self::TEST_USERNAME2;
            $username3 = self::TEST_USERNAME3;

            // now create user1 and generate code pair
            $licType = LicenseType::find(1);
            $licFuncType = \Phonex\LicenseFuncType::getTrial();
            $command = new CreateUser($username1);
            $user1 = Bus::dispatch($command);
            $commandSub = new CreateSubscriberWithLicense($user1, $licType, $licFuncType, 'fasirka_heslo');
            Bus::dispatch($commandSub);

            $command = new CreateBusinessCodePair($user1, $licType, $licFuncType);
            // add parent - will be added as support account
            $command->addParent($user1);
            $codePair = Bus::dispatch($command);

            // use 1. code
            $this->callAndCheckResponse(
                self::URL,
                ['version' => AccountController::VERSION, 'imei' => 'a','captcha' =>'c', 'username' => $username2,'bcode' => $codePair[0]->code],
                AccountController::RESP_OK
            );

            // use 2. code
            $this->callAndCheckResponse(
                self::URL,
                ['version' => AccountController::VERSION, 'imei' => 'a','captcha' =>'c', 'username' => $username3,'bcode' => $codePair[1]->code],
                AccountController::RESP_OK
            );

            // check both have themselves and support account in cl
            $user1 = User::findByUsername($username1);
            $user2 = User::findByUsername($username2);
            $user3 = User::findByUsername($username3);

            $this->assertTrue($user2->subscriber->subscribersInContactList->contains($user1->subscriber));
            $this->assertTrue($user2->subscriber->subscribersInContactList->contains($user3->subscriber));

            $this->assertTrue($user3->subscriber->subscribersInContactList->contains($user1->subscriber));
            $this->assertTrue($user3->subscriber->subscribersInContactList->contains($user2->subscriber));
        } finally {
            $this->deleteSubscribers([$username1, $username2, $username3]);
        }
    }
}