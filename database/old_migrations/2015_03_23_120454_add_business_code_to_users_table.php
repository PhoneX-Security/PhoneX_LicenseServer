<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBusinessCodeToUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('phonex_users', function(Blueprint $table)
		{
            $table->unsignedInteger('business_code_id')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('phonex_users', function(Blueprint $table)
		{
            $table->dropColumn('business_code_id');
		});
	}

}
