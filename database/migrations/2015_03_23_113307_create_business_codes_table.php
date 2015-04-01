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
            $table->engine = 'InnoDB';

			$table->increments('id');
			$table->timestamps();
            $table->string('code');
            $table->unsignedInteger('group_id'); // fk to groups table
            $table->unsignedInteger('creator_id'); // fk to users table
            $table->unsignedInteger('license_type_id');
            $table->integer('licenses_limit');
            $table->boolean('is_active')->default(true);
            $table->boolean('exported')->default(false);
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
