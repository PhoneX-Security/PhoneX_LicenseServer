<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLicensesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('phonex_licenses', function(Blueprint $table)
		{
            $table->engine = 'InnoDB';

			$table->increments('id');
			$table->unsignedInteger('user_id');
			$table->unsignedInteger('license_type_id');
            $table->unsignedInteger('issuer_id')->nullable();
			$table->timestamps();
			$table->boolean('is_activated')->default(false);
			$table->timestamp('starts_at')->nullable();
			$table->timestamp('expires_at')->nullable();
			$table->text('comment');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('phonex_licenses');
	}

}
