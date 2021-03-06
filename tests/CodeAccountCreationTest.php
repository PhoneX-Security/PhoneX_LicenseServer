<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Phonex\Group;
use Phonex\Http\Controllers\AccountController;
use Phonex\Jobs\CreateUserWithSubscriber;
use Phonex\Jobs\IssueProductLicense;
use Phonex\Jobs\NewCodePairsExport;
use Phonex\License;
use Phonex\Model\NotificationType;
use Phonex\Model\Product;
use Phonex\Model\SupportNotification;
use Phonex\Subscriber;
use Phonex\User;

class CodeAccountCreationTest extends TestCase {
    use DatabaseTransactions;

    const URL = '/account/business-account';
    const TEST_USERNAME = "ckajsme_sk1";
    const TEST_USERNAME2 = "ckozmker_sk";
    const TEST_USERNAME3 = "cfansmeker_sk";
    const TEST_USERNAME_NON_EXISTING = "kajsmeker11";

    const GROUP_NAME = "slovensko";

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

    public function testExpiredBusinessCode(){
        $product = Product::getFullMonth();

        $c1 = new NewCodePairsExport(1, $product, 1);
        // code from past - this should fail
        $c1->addExpiration(Carbon::createFromDate(1999));
        list($export, $codes) = Bus::dispatch($c1);

        $this->callAndCheckResponse(self::URL, [
            'version' => AccountController::VERSION,
            'imei' => 'a',
            'captcha' =>'captcha',
            'username' => self::TEST_USERNAME_NON_EXISTING,
            'bcode' => $codes[0][0]->code
        ], AccountController::RESP_ERR_EXPIRED_BUSINESS_CODE);
    }

    /**
     * Main test
     */
    public function testAccountCreation2(){
        try {
            $username1 = self::TEST_USERNAME;
            $username2 = self::TEST_USERNAME2;
            $username3 = self::TEST_USERNAME3;

            $product = Product::getTrialWeek();

            $command = new CreateUserWithSubscriber($username1, "fasirka_heslo");
            $user1 = Bus::dispatch($command);
            $commandSub = new IssueProductLicense($user1, $product);
            Bus::dispatch($commandSub);

            // predefined group + license type
            $group = Group::create(['name' => self::GROUP_NAME]);

            $command = new NewCodePairsExport(1, $product, 1);
            $command->addGroup($group);
            list($export1, $codes1) = Bus::dispatch($command);

            // remember counts
            $userCount = User::all()->count();
            // new: license is not issued automatically
//            $licenseCount = License::all()->count();
            $subscriberCount = Subscriber::all()->count();

            // now use first code to get a license
            $json = $this->callAndCheckResponse(self::URL,[
                'version' => AccountController::VERSION,
                'imei' => 'a',
                'captcha' =>'captcha',
                'username' => $username2,
                'bcode' => $codes1[0][0]->code
            ], AccountController::RESP_OK);
            $this->assertEquals($username2, $json->username);

            // assert all records created
            $this->assertEquals($userCount + 1, User::all()->count());
//            $this->assertEquals($licenseCount + 1, License::all()->count());
            $this->assertEquals($subscriberCount + 1, Subscriber::all()->count());

            // check we cannot create another license on the same business code
            $this->callAndCheckResponse(self::URL, [
                'version' => AccountController::VERSION,
                'imei' => 'a',
                'captcha' =>'captcha',
                'username' => $username3,
                'bcode' => $codes1[0][0]->code
            ], AccountController::RESP_ERR_ALREADY_USED_BUSINESS_CODE);

            // now use the second code to get a license
            $json = $this->callAndCheckResponse(self::URL, [
                'version' => AccountController::VERSION,
                'imei' => 'a',
                'captcha' =>'captcha',
                'username' => $username3,
                'bcode' => $codes1[0][1]->code
            ], AccountController::RESP_OK);
            $this->assertEquals($username3, $json->username);


            // check user has welcome message dispatched
            $notification = SupportNotification::where(['user_id' => $user1->id, 'notification_type_id' => NotificationType::getWelcomeNotification()->id])->first();
            $this->assertNotNull($notification);

            // expect two users in contact list (support and another business user)
            $user3 = User::where('username', $username3)->first();
            $this->assertEquals(2, count($user3->subscriber->subscribersInContactList));


//        // assert all records created
            $this->assertEquals($userCount + 2, User::all()->count());
//            $this->assertEquals($licenseCount + 2, License::all()->count());
            $this->assertEquals($subscriberCount + 2, Subscriber::all()->count());

        } finally {
            // Subscribers table do not support transactions, delete manually
            $this->deleteSubscribers([$username1, $username2, $username3]);
        }
    }

    public function testAccountCreationWithParent(){
        try {
            $username1 = self::TEST_USERNAME;
            $username2 = self::TEST_USERNAME2;
            $username3 = self::TEST_USERNAME3;

            // now create user1 and generate code pair
            $product = Product::getTrialWeek();
            $command = new CreateUserWithSubscriber($username1, "fasirka_heslo");
            $user1 = Bus::dispatch($command);
            $commandSub = new IssueProductLicense($user1, $product);
            Bus::dispatch($commandSub);

            $command = new NewCodePairsExport(1, $product);
            // some future expiration
            $command->addExpiration(Carbon::now()->addYears(2));
            $command->addParent($user1);
            list($export1, $codes) = Bus::dispatch($command);

            // use 1. code
            $this->callAndCheckResponse(
                self::URL,
                ['version' => AccountController::VERSION, 'imei' => 'a','captcha' =>'c', 'username' => $username2,'bcode' => $codes[0][0]->code],
                AccountController::RESP_OK
            );

            // use 2. code
            $this->callAndCheckResponse(
                self::URL,
                ['version' => AccountController::VERSION, 'imei' => 'a','captcha' =>'c', 'username' => $username3,'bcode' => $codes[0][1]->code],
                AccountController::RESP_OK
            );

            // check both have themselves and support account in cl
//            $user1 = User::findByUsername($username1);

            $supportUser = User::getSupportUser();
            $user2 = User::findByUsername($username2);
            $user3 = User::findByUsername($username3);

//            $this->assertTrue($user2->subscriber->subscribersInContactList->contains($user1->subscriber));
            $this->assertTrue($user2->subscriber->subscribersInContactList->contains($supportUser->subscriber));
            $this->assertTrue($user2->subscriber->subscribersInContactList->contains($user3->subscriber));

//            $this->assertTrue($user3->subscriber->subscribersInContactList->contains($user1->subscriber));
            $this->assertTrue($user3->subscriber->subscribersInContactList->contains($supportUser->subscriber));
            $this->assertTrue($user3->subscriber->subscribersInContactList->contains($user2->subscriber));
        } finally {
            $this->deleteSubscribers([$username1, $username2, $username3]);
        }
    }

    public function testAccountCreationWithGroupOwner(){
        try {
            $username1 = self::TEST_USERNAME;
            $username2 = self::TEST_USERNAME2;

            $product = Product::getTrialWeek();
            $command = new CreateUserWithSubscriber($username1, "fasirka_heslo");
            $user1 = Bus::dispatch($command);
            $commandSub = new IssueProductLicense($user1, $product);
            Bus::dispatch($commandSub);

            $group = Group::create(['name' => self::GROUP_NAME, 'owner_id' => $user1->id]);

            $command = new NewCodePairsExport(1, $product);
            $command->addGroup($group);
            list($exp, $codes) = Bus::dispatch($command);

            // use 1. code
            $this->callAndCheckResponse(
                self::URL,
                ['version' => AccountController::VERSION, 'imei' => 'a','captcha' =>'c', 'username' => $username2,'bcode' => $codes[0][0]->code],
                AccountController::RESP_OK
            );

            // check both have themselves and support account in cl
            // check user2 has user1 as a support account (as a owner of a group)
            $user2 = User::findByUsername($username2);
            $supportUser = User::getSupportUser();

//            $this->assertTrue($user2->subscriber->subscribersInContactList->contains($user1->subscriber));
            $this->assertTrue($user2->subscriber->subscribersInContactList->contains($supportUser->subscriber));
        } finally {
            $this->deleteSubscribers([$username1, $username2]);
        }
    }
}