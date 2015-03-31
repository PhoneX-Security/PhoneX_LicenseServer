<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusinessCodeUserClMappingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('business_code_user_cl_mappings', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
            $table->unsignedInteger('bcode_id');
            $table->unsignedInteger('user_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('business_code_user_cl_mappings');
	}

}
