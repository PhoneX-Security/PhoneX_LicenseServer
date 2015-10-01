<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Phonex\BusinessCode;
use Phonex\Group;
use Phonex\Jobs\NewCodePairsExport;
use Phonex\Model\Product;
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
        $product = Product::getTrialWeek();
        $date = Carbon::createFromDate(1999);

        $c1 = new NewCodePairsExport(2, $product, 1);
        $c1->addExpiration($date);
        $c1->addGroup($group);
        list($export, $codes) = Bus::dispatch($c1);

        // reload code and check its properties
        $code = BusinessCode::where('code', $codes[0][0]->code)->first();
//        $this->assertEquals($product->licenseType->id, $code->getLicenseType()->id);
//        $this->assertEquals($product->licenseFuncType->id, $code->getLicenseFuncType()->id);

        $this->assertEquals($group->id, $code->getGroup()->id);
        $this->assertNull($code->getParent());
        $this->assertTrue($code->getExpiresAt()->eq($date));
        $this->assertEquals(1, $code->getLicenseLimit());

        // now add attributes to code itself, overriding those belonging to export
        $group2 = Group::create(['name' => 'testGroup2']);
        $date2 = Carbon::createFromDate(2008);
        $parent = User::find(1);

        $code->group_id = $group2->id;
        $code->parent_id = $parent->id;
        $code->expires_at = $date2;
        $code->save();

        // reload
        $code = BusinessCode::where('code', $code->code)->first();

        // test again

        $this->assertEquals($group2->id, $code->getGroup()->id);
        $this->assertEquals($parent->id, $code->getParent()->id);
        $this->assertTrue($code->getExpiresAt()->eq($date2));
    }
}