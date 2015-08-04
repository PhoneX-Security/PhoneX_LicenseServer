<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('hash_id', 40);
            $table->unique('hash_id');

            $table->timestamps();
            $table->string('email');
            $table->boolean('existing_users');
            $table->smallInteger('state');

            $table->text('note');
            $table->string('locale', 10);
            $table->string('currency');
            $table->decimal('total_cost_no_vat',10,2);
            $table->decimal('vat',10,2);
            $table->decimal('total_cost',10,2);

            $table->integer('gopay_id')->nullable();
            $table->integer('gopay_result_code')->nullable();

            $table->integer('business_codes_export_id')->unsigned()->nullable();
            $table->foreign('business_codes_export_id')->references('id')->on('business_codes_exports');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('orders');
    }
}
