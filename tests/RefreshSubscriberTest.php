<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Phonex\User;

class RefreshSubscriberTest extends TestCase {
    use DatabaseTransactions;
    const TEST_NAME_1 = "jano17xc";

    public function setUp()
    {
        parent::setUp();
        $user = User::find(1);
        $this->be($user);
    }

    public function testRetrieveActiveLicense()
    {

//        try {
//            $user = $this->createUser(self::TEST_NAME_1);
//
//            $licType = LicenseType::find(1);
//            $licFuncType = LicenseFuncType::getFull();
//
//            $c2 = new CreateSubscriberWithLicense($user, $licType, $licFuncType, "pass");
//            $c2->startingAt(Carbon::createFromDate(2023));
//            $l2 = Bus::dispatch($c2);
//
//            $c = new IssueLicense($user, $licType, $licFuncType, "pass");
//            $l1 = Bus::dispatch($c);
//
//            $recentLic = RefreshSubscribers::getActiveLicenseWithLatestExpiration($user);
//            $this->assertEquals($l1->id, $recentLic->id);
//        } finally {
//            $user = User::where('username', self::TEST_NAME_1)->first();
//            if ($user){
//                $user->subscriber->deleteWithContactListRecords();
//            }
//        }

    }
//
//    public function testChangeLicAndRunUpdate()
//    {
//        try {
//            $user = $this->createUser(self::TEST_NAME_1);
//
//            $licWeekType = LicenseType::getWeek();
//            $licMonthType = LicenseType::getMonth();
//            $licFuncType = LicenseFuncType::getFull();
//
//            $c1 = new CreateSubscriberWithLicense($user, $licMonthType, $licFuncType, "pass");
//            $l1 = Bus::dispatch($c1);
//
//            $c2 = new IssueLicense($user, $licWeekType, $licFuncType, "pass");
//            $c2->startingAt(Carbon::now()->subDays(3));
//            $l2 = Bus::dispatch($c2);
//
//            // Refresh subscribers -- the first license should override expiration in subscriber
//            $refreshSubscribers = new RefreshSubscribers();
//            Bus::dispatch($refreshSubscribers);
//
//            // test if that happened
//            $user = User::find($user->id); // reload user
//            $this->assertTrue($user->active_license_id === ($l1->id));
//            $this->assertTrue($user->subscriber->issued_on->eq($l1->starts_at));
//            $this->assertTrue($user->subscriber->expires_on->eq($l1->expires_at));
//        } finally {
//            $user = User::where('username', self::TEST_NAME_1)->first();
//            if ($user){
//                $user->subscriber->deleteWithContactListRecords();
//            }
//        }
//
//    }
}