<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('product', function($table)
        {
                $table->increments('product_id');
                $table->string('name');
                $table->string('identity');
                $table->string('image');
                $table->integer('category');
                $table->string('meta_keyword');
                $table->string('description');
                $table->string('summary');
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
		Schema::drop('product');
	}

}
