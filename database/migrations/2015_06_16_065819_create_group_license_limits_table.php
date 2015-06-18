<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupLicenseLimitsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('group_license_limits', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
            $table->timestamp('starts_at');
            $table->timestamp('expires_at');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('group_license_limits');
	}

}
