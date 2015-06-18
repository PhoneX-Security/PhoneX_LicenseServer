<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrialRequestsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('phonex_trial_requests', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->timestamp('dateCreated');
            $table->timestamp('dateUpdated');

            $table->string('username')->nullable();
            $table->string('captcha', 20);
            $table->string('imei', 50);
            $table->string('ip', 50);
            $table->boolean('isApproved', 50)->nullable();
            $table->boolean('phonexUserId')->nullable();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('phonex_trial_requests');
	}

}
