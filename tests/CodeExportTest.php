<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Phonex\BusinessCode;
use Phonex\Jobs\CreateBusinessCodePair;
use Phonex\Jobs\CreateSubscriberWithLicense;
use Phonex\Jobs\CreateUser;
use Phonex\Group;
use Phonex\Http\Controllers\AccountController;
use Phonex\Jobs\NewCodePairsExport;
use Phonex\License;
use Phonex\LicenseFuncType;
use Phonex\LicenseType;
use Phonex\Subscriber;
use Phonex\User;

class CodeExportTest extends TestCase {
    use DatabaseTransactions;

    public function setUp(){
        // has to do this here before the framework is started because phpunit prints something before headers are sent
        @session_start();
        parent::setUp();

        // login as random user
        $user = User::find(1);
        $this->be($user);
    }

    public function testExportAttributes()
    {
        $group = Group::create(['name' => 'testGroup']);
        $licenseType = LicenseType::getWeek();
        $licenseFuncType = LicenseFuncType::getTrial();
        $date = Carbon::createFromDate(1999);

        $c1 = new NewCodePairsExport(2, $licenseType, $licenseFuncType, 1);
        $c1->addExpiration($date);
        $c1->addGroup($group);
        list($export, $codes) = Bus::dispatch($c1);

        // reload code and check its properties
        $code = BusinessCode::where('code', $codes[0][0]->code)->first();
        $this->assertEquals($licenseType->id, $code->getLicenseType()->id);
        $this->assertEquals($licenseFuncType->id, $code->getLicenseFuncType()->id);

        $this->assertEquals($group->id, $code->getGroup()->id);
        $this->assertNull($code->getParent());
        $this->assertTrue($code->getExpiresAt()->eq($date));
        $this->assertEquals(1, $code->getLicenseLimit());

        // now add attributes to code itself, overriding those belonging to export
        $group2 = Group::create(['name' => 'testGroup2']);
        $licenseType2 = LicenseType::getHalfYear();
        $licenseFuncType2 = LicenseFuncType::getFull();
        $date2 = Carbon::createFromDate(2008);
        $parent = User::find(1);

        $code->license_type_id = $licenseType2->id;
        $code->license_func_type_id = $licenseFuncType2->id;
        $code->group_id = $group2->id;
        $code->parent_id = $parent->id;
        $code->expires_at = $date2;
        $code->save();

        // reload
        $code = BusinessCode::where('code', $code->code)->first();

        // test again
        $this->assertEquals($licenseType2->id, $code->getLicenseType()->id);
        $this->assertEquals($licenseFuncType2->id, $code->getLicenseFuncType()->id);

        $this->assertEquals($group2->id, $code->getGroup()->id);
        $this->assertEquals($parent->id, $code->getParent()->id);
        $this->assertTrue($code->getExpiresAt()->eq($date2));
    }
}