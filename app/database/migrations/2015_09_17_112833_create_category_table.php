<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('category', function($table)
        {
                $table->increments('category_id');
                $table->string('identity');
                $table->string('name');
                $table->tinyInteger('sex');
                $table->tinyInteger('enable')->default(1);
                $table->string('discription');
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
		Schema::drop('category');
	}

}
