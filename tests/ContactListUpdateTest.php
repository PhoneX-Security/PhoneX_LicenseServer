<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Phonex\ContactList;
use Phonex\Jobs\CreateUser;
use Phonex\Jobs\CreateUserWithSubscriber;
use Phonex\Jobs\IssueProductLicense;
use Phonex\Model\Product;
use Phonex\User;

class ContactListUpdateTest extends TestCase {
    use DatabaseTransactions;
    const TEST_USER1 = "smajda42";
    const TEST_USER2 = "smajdova_mamka31";

    public function setUp()
    {
        parent::setUp();
        $user = User::find(1);
        $this->be($user);
    }

    public function testContactListUpdate(){

//        $oldUser = User::where('username', self::TEST_USER1)->first();
//        if ($oldUser != null){
//            $oldUser->deleteWithLicenses();
//        }
//        $oldUser2 = User::where('username', self::TEST_USER2)->first();
//        if ($oldUser2 != null){
//            $oldUser2->deleteWithLicenses();
//        }


        try {
            $product = Product::getFullMonth();

            $clCount = ContactList::all()->count();

            $u1 = Bus::dispatch(new CreateUserWithSubscriber(self::TEST_USER1, "pass"));
            $u2 = Bus::dispatch(new CreateUserWithSubscriber(self::TEST_USER2, "pass"));
            Bus::dispatch(new IssueProductLicense($u1, $product));
            Bus::dispatch(new IssueProductLicense($u2, $product));

            $u1->addToContactList($u2);
            $this->assertTrue($u1->subscriber->subscribersInContactList->contains($u2->subscriber));
            $this->assertFalse($u2->subscriber->subscribersInContactList->contains($u1->subscriber));

            $this->assertEquals($clCount + 1, ContactList::all()->count());

            $u2->addToContactList($u1);
            // reload model (stale model causes problems)
            $u2 = User::find($u2->id);

            $this->assertEquals($clCount + 2, ContactList::all()->count());
            $this->assertTrue($u2->subscriber->subscribersInContactList->contains($u1->subscriber));

            // assert exception
            $this->setExpectedException('\Phonex\Exceptions\SubscriberAlreadyInCLException');
            $u2->addToContactList($u1);
        } finally {
            $user = User::where('username', self::TEST_USER1)->first();
            if ($user){
                $user->subscriber->deleteWithContactListRecords();
            }
            $user = User::where('username', self::TEST_USER2)->first();
            if ($user){
                $user->subscriber->deleteWithContactListRecords();
            }
        }
    }
}