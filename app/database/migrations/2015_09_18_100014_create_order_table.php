<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order', function($table)
        {
                $table->increments('order_id');
                $table->integer('user_id');
                $table->string('customer_name');
                $table->string('customer_phone');
                $table->string('customer_email');
                $table->string('customer_address');
                $table->integer('customer_district');
                $table->integer('customer_province');
                $table->integer('total_pay')->default(0);
                $table->tinyInteger('status')->default(1);
                $table->string('note');
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
		Schema::drop('order');
	}

}
