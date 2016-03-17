<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('tiles', function(Blueprint $t) {
			$t->increments('id');
			$t->string('type')->default('image');
			$t->text('content');
			$t->string('width')->default('auto');
			$t->string('height')->default('auto');
			$t->text('options')->nullable();
			$t->integer('order');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tiles');
    }
}
