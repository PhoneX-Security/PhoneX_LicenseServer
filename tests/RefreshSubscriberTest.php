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
    const TEST_NAME_1 = "refresh_subscriber_user1";
    const TEST_NAME_2 = "refresh_subscriber_user_b13";

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

            // test that by default "default" product permissions are issued

            // get subscription and consumable products
            $trialSubscriptionProduct = Product::getTrialWeek();
            $fullSubscriptionProduct = Product::getFullMonth();
            $defaultSubscriptionProduct = Product::getDefault();
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
            // default lic permission
            $subscriptionsPermissionsCount += $defaultSubscriptionProduct->appPermissions->count();

            // we have two consumables
            $consumablePermissionsCount += 2 * $consumableProduct->appPermissions->count();

            $this->assertEquals($subscriptionsPermissionsCount, count($usagePolicyCurrent->subscriptions ));
            $this->assertEquals($consumablePermissionsCount, count($usagePolicyCurrent->consumables));

        } finally {
            $this->deleteSubscribers([self::TEST_NAME_1]);
        }
    }

    public function testPolicyTimestampRefresh(){
        try {
            $command = new CreateUserWithSubscriber(self::TEST_NAME_2, "fasirka_heslo");
            $user1 = Bus::dispatch($command);
            $this->assertNull($user1->subscriber->usage_policy_current);

            // Get subscription of product with limited permissions
            $trialSubscription = Product::findByName('inapp.subs.basic.month');
            $this->assertNotNull($trialSubscription);
            $licCommand1 = new IssueProductLicense($user1, $trialSubscription);
            Bus::dispatch($licCommand1);

            // Reload user
            $userA = User::findByUsername($user1->username);
            $timestamp1 = json_decode($userA->subscriber->usage_policy_current)->timestamp;

            // Timestamp is based on seconds
            // Wait a few seconds before refreshing
            sleep(2);
            RefreshSubscribers::refreshSingleUser($userA);

            $userB = User::findByUsername($user1->username);
            $timestamp2 = json_decode($userB->subscriber->usage_policy_current)->timestamp;

            $this->assertEquals($timestamp1, $timestamp2, "Timestamp should not have been updated, but the value was changed.");

            // Issue new product (full license) and check the timestamp value again - now it should be updated
            $fullSubscriptionProduct = Product::getFullMonth();
            $licCommand2 = new IssueProductLicense($userB, $fullSubscriptionProduct);
            Bus::dispatch($licCommand2);

            sleep(2);
            RefreshSubscribers::refreshSingleUser($user1);

            // Reload user
            $userC = User::findByUsername($user1->username);
            $timestamp3 = json_decode($userC->subscriber->usage_policy_current)->timestamp;
            $this->assertGreaterThan($timestamp1, $timestamp3, "Timestamp is not greater than the old one");
        } finally {
            $this->deleteSubscribers([self::TEST_NAME_2]);
        }
    }
}