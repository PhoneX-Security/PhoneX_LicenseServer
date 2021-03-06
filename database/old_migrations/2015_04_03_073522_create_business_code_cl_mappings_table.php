<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusinessCodeClMappingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('business_code_cl_mappings', function(Blueprint $table)
		{
            $table->engine = 'InnoDB';

			$table->increments('id');
			$table->timestamps();
            $table->unsignedInteger('cl_owner_bcode_id');
            $table->unsignedInteger('contact_bcode_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('business_codes_cl_mappings');
	}

}
