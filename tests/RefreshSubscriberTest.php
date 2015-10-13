<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Phonex\Jobs\CreateUserWithSubscriber;
use Phonex\Jobs\IssueProductLicense;
use Phonex\Jobs\RefreshSubscribers;
use Phonex\Model\Product;
use Phonex\User;

class RefreshSubscriberTest extends TestCase {
    use DatabaseTransactions;
    const TEST_NAME_1 = "jano24xc";

    public function setUp()
    {
        parent::setUp();
        $user = User::find(1);
        $this->be($user);
    }

    public function testRefreshPermissions()
    {

        try {
            // create user1
            $command = new CreateUserWithSubscriber(self::TEST_NAME_1, "fasirka_heslo");
            $user1 = Bus::dispatch($command);

            // get subscription and consumable products
            $trialSubscriptionProduct = Product::getTrialWeek();
            $fullSubscriptionProduct = Product::getFullMonth();
            // todo change hardcoded product id (inapp_consumable_calls30)
            $consumableProduct = Product::find(7);

            // issue licenses to products
            $licCommand1 = new IssueProductLicense($user1, $trialSubscriptionProduct);
            $licCurrent = Bus::dispatch($licCommand1);

            $licCommand2 = new IssueProductLicense($user1, $fullSubscriptionProduct);
            $licCommand2->startingAt(Carbon::now()->subYears(2));
            $licPast = Bus::dispatch($licCommand2);

            $licCommand3 = new IssueProductLicense($user1, $fullSubscriptionProduct);
            $licCommand3->startingAt(Carbon::now()->addMonths(6));
            $licFuture = Bus::dispatch($licCommand3);

            $licCommand4 = new IssueProductLicense($user1, $consumableProduct);
            $consumableCurrent = Bus::dispatch($licCommand4);

            $licCommand5 = new IssueProductLicense($user1, $consumableProduct);
            $licCommand5->startingAt(Carbon::now()->addMonths(6));
            $consumableFuture = Bus::dispatch($licCommand5);

            // reload user
            $user1 = User::findByUsername($user1->username);
            RefreshSubscribers::refreshSingleUser($user1);
            $subscriber = $user1->subscriber;

            // check subscriber policies and expiration is correct
            $this->assertTrue($subscriber->issued_on->eq($licCurrent->starts_at));
            $this->assertTrue($subscriber->expires_on->eq($licCurrent->expires_at));
            $this->assertEquals($licCurrent->product->licenseFuncType->name, $subscriber->license_type);


            $usagePolicyCurrent = json_decode($subscriber->usage_policy_current);

            $trialSubscriptionProduct->loadPermissionsFromParentIfMissing();
            $fullSubscriptionProduct->loadPermissionsFromParentIfMissing();
            $consumableProduct->loadPermissionsFromParentIfMissing();

            $subscriptionsPermissionsCount = 0;
            $consumablePermissionsCount = 0;

            // current lic permissions
            $subscriptionsPermissionsCount += $trialSubscriptionProduct->appPermissions->count();
            // future lic permissions
            $subscriptionsPermissionsCount += $fullSubscriptionProduct->appPermissions->count();

            // we have two consumables
            $consumablePermissionsCount += 2 * $consumableProduct->appPermissions->count();

            $this->assertEquals($subscriptionsPermissionsCount, count($usagePolicyCurrent->subscriptions));
            $this->assertEquals($consumablePermissionsCount, count($usagePolicyCurrent->consumables));
        } finally {
            $this->deleteSubscribers([self::TEST_NAME_1]);
        }
    }
}