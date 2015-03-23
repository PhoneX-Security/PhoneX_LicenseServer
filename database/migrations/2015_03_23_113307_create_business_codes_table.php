<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusinessCodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('business_codes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
            $table->string('code');
            $table->unsignedInteger('owner_id'); // fk to users tables
            $table->unsignedInteger('license_type_id');
            $table->integer('licenses_limit');
            $table->boolean('is_active')->default(true);
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('business_codes');
	}

}
