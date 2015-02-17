<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuditTrailTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('audit_trail', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
            $table->unsignedInteger('user_id');
            $table->string('operation');
            $table->string('entity_name')->nullable();
            $table->unsignedInteger('entity_id')->nullable();
            $table->string('field_name')->nullable();
            $table->string('old_value')->nullable();
            $table->string('new_value')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('audit_trail');
	}

}
