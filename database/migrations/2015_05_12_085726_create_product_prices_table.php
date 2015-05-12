<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductPricesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('product_prices', function(Blueprint $table)
		{
            $table->engine = 'InnoDB';
			$table->increments('id');
			$table->timestamps();

            $table->string('currency', 10);
            $table->decimal('cost', 10, 2);

            // fk products
            $table->integer('product_id')->unsigned();
            $table
                ->foreign('product_id')
                ->references('id')->on('products');


		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('product_prices');
	}

}
