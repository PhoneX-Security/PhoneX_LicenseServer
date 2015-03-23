<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserOrganization extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_organization', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('organization_id');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_organization');
	}

}
