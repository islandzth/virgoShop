<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderMetaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order_meta', function($table)
        {
            $table->increments('id');
            $table->integer('order_id');
            $table->integer('product_meta_id');
            $table->integer('gia');
            $table->integer('quantity');
            $table->integer('total_price');
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
		Schema::drop('order_meta');
	}

}
