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
        LicenseFuncType::firstOrCreate(['name' => LicenseFuncType::TYPE_TRIAL]);
        LicenseFuncType::firstOrCreate(['name' => LicenseFuncType::TYPE_FULL]);
        LicenseFuncType::firstOrCreate(['name' => LicenseFuncType::TYPE_CALL_ME_CLIENT]);
        LicenseFuncType::firstOrCreate(['name' => LicenseFuncType::TYPE_POOL_CLIENT]);
        LicenseFuncType::firstOrCreate(['name' => LicenseFuncType::TYPE_COMPANY_CLIENT]);
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
