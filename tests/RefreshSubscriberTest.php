<?php

use Carbon\Carbon;
use Phonex\Jobs\CreateSubscriberWithLicense;
use Phonex\Jobs\IssueLicense;
use Phonex\Jobs\RefreshSubscribers;
use Phonex\LicenseFuncType;
use Phonex\LicenseType;
use Phonex\User;

class RefreshSubscriberTest extends TestCase {
    const TEST_NAME_1 = "jano";

    public function setUp()
    {
        parent::setUp();
        $user = User::find(1);
        $this->be($user);
    }


    public function testRetrieveActiveLicense(){
        $this->mockQueuePush(); //dirtyfix

        $user = $this->createUser(self::TEST_NAME_1);

        $licType = LicenseType::find(1);
        $licFuncType = LicenseFuncType::getFull();

        $c2 = new CreateSubscriberWithLicense($user, $licType, $licFuncType, "pass");
        $c2->startingAt(Carbon::createFromDate(2023));
        $l2 = Bus::dispatch($c2);

        $c = new IssueLicense($user, $licType, $licFuncType, "pass");
        $l1 = Bus::dispatch($c);

//        $recentLic = RefreshSubscribers::getActiveLicense($user);
        $recentLic = $user->getActiveLicenseWithLatestExpiration();
        $this->assertEquals($l1->id, $recentLic->id);
    }

	public function testRetrieveFutureLicense(){
        // TODO rework, functionality has changed
//        $this->mockQueuePush(); //dirtyfix
//
//        $user = $this->createUser(self::TEST_NAME_1);
//
//        $licType = LicenseType::find(1);
//        $licFuncType = LicenseFuncType::getFull();
//
//        $c1 = new CreateSubscriberWithLicense($user, $licType, $licFuncType, "pass");
//        $c1->startingAt(Carbon::createFromDate(2023));
//        $l1 = Bus::dispatch($c1);
//
//        $c2 = new IssueLicense($user, $licType, $licFuncType, "pass");
//        $c2->startingAt(Carbon::createFromDate(2025));
//        $l2 = Bus::dispatch($c2);
//
//        $c3 = new IssueLicense($user, $licType, $licFuncType, "pass");
//        $c3->startingAt(Carbon::createFromDate(2020));
//        $l3 = Bus::dispatch($c3);
//
////        $recentLic = RefreshSubscribers::getActiveLicense($user);
//        $recentLic = $user->getActiveLicenseWithLatestExpiration();
//
//        dd($recentLic);
//
//        $this->assertEquals($l3->id, $recentLic->id);
	}
}