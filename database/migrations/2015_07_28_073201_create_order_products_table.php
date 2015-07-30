<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_products_table', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('quantity');
            $table->string('currency');
            $table->decimal('cost',10,2);
            $table->smallInteger('order')->default(0);

            $table->string('name');
            $table->string('display_name');

            //fk
            $table->integer('order_id')->unsigned();
            $table->foreign('order_id')->references('id')->on('orders');

            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('products');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('order_products_table');
    }
}
