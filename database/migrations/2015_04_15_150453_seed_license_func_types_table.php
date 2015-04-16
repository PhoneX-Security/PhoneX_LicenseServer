<?php

use Illuminate\Database\Migrations\Migration;
use Phonex\LicenseFuncType;

class SeedLicenseFuncTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        LicenseFuncType::create(['name' => LicenseFuncType::TYPE_TRIAL, 'order' => 1]);
        LicenseFuncType::create(['name' => LicenseFuncType::TYPE_FULL, 'order' => 2]);
        LicenseFuncType::create(['name' => LicenseFuncType::TYPE_CALL_ME_CLIENT, 'order' => 3]);
        LicenseFuncType::create(['name' => LicenseFuncType::TYPE_POOL_CLIENT, 'order' => 4]);
        LicenseFuncType::create(['name' => LicenseFuncType::TYPE_COMPANY_CLIENT, 'order' => 5]);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}
