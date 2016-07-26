<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductMetaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('product_meta', function($table)
        {
            $table->increments('id');
            $table->integer('product_id');
            $table->string('size');
            $table->integer('gia');
            $table->tinyInteger('enable')->default(1);
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
		Schema::drop('product_meta');
	}

}
