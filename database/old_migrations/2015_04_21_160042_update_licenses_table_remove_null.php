<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Phonex\License;

class UpdateLicensesTableRemoveNull extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        License::whereNull('starts_at')->update(['starts_at' => Carbon::createFromDate(2013, 9, 1)]);
        // let them expireeee!
        License::whereNull('expires_at')->update(['expires_at' => Carbon::createFromDate(2015, 7, 1)]);
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
