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
		Schema::create('phonex_users', function(Blueprint $table)
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
            // legacy, not in USE (now licenses are trials, not users)
			$table->boolean('isTrial')->default(false);
            // QA trial user - flag to mark users that are for QA testing
            $table->boolean('qa_trial')->default(false);
			$table->integer('trialNumber');
			// foreign keys
			$table->unsignedInteger('subscriber_id'); // referencing opensips db, table subscriber
			$table->unsignedInteger('parent_id'); // self referencing

            $table->text('comment');
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
		Schema::drop('phonex_users');
	}

}
