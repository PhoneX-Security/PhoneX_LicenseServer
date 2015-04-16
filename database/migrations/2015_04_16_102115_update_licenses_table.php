<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Phonex\LicenseFuncType;

class UpdateLicensesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$full = LicenseFuncType::getFull();
		$trial = LicenseFuncType::getTrial();

        $weekLicenseTypeId = 1; //former trial, currently week license type (=meaning length)
        //
        // trial (in new version week) license type is ID 1
        DB::statement("update phonex_licenses set license_func_type_id = ? where license_type_id = ?", [$trial->id, $weekLicenseTypeId]);
        // full (everything else other than trial at the moment)
        DB::statement("update phonex_licenses set license_func_type_id = ? where license_type_id <> ?", [$full->id, $weekLicenseTypeId]);
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
