<?php

use Phonex\Commands\CreateSubscriberWithLicense;
use Phonex\Commands\CreateUser;
use Phonex\ContactList;
use Phonex\LicenseFuncType;
use Phonex\LicenseType;
use Phonex\User;

class ContactListUpdateTest extends TestCase {
    const TEST_USER1 = "smajda";
    const TEST_USER2 = "smajdova_mamka";

    public function testContactListUpdate(){

        $oldUser = User::where('username', self::TEST_USER1)->first();
        if ($oldUser != null){
            $oldUser->deleteWithLicenses();
        }
        $oldUser2 = User::where('username', self::TEST_USER2)->first();
        if ($oldUser2 != null){
            $oldUser2->deleteWithLicenses();
        }

        $licType = LicenseType::find(1);
        $licFuncType = LicenseFuncType::getFull();

        $clCount = ContactList::all()->count();

        $u1 = Bus::dispatch(new CreateUser(self::TEST_USER1));
        $u2 = Bus::dispatch(new CreateUser(self::TEST_USER2));

        Bus::dispatch(new CreateSubscriberWithLicense($u1, $licType, $licFuncType, "pass"));
        Bus::dispatch(new CreateSubscriberWithLicense($u2, $licType, $licFuncType, "pass"));

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
    }
}