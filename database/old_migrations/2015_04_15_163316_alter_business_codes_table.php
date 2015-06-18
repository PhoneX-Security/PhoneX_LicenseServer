<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterBusinessCodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('business_codes', function(Blueprint $table)
		{
            $table->unsignedInteger('license_func_type_id');
			//
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('business_codes', function(Blueprint $table)
		{
			$table->dropColumn('license_func_type_id');
		});
	}

}
