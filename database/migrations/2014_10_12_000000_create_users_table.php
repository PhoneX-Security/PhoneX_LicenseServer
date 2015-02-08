<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('username');
			$table->string('email')->unique()->nullable();
			$table->string('password', 60);
			$table->boolean('has_access')->default(false);
			$table->rememberToken();
			// kind of legacy attributes
			$table->string('confirmEmail');
			$table->string('confirmNonce', 100);
			$table->boolean('confirmed');
			$table->boolean('isTrial')->default(false);
			$table->integer('trialNumber');
			// foreign keys
			$table->unsignedInteger('subscriber_id'); // referencing opensips db, table subscriber
			$table->unsignedInteger('parent_id'); // self referencing

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
