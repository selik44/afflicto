<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('product_id')->nullable()->default(null);
            $table->integer('user_id')->nullable()->default(null);
            $table->integer('rating')->nullable()->default(null);
            $table->text('comment')->nullable()->default(null);
            $table->tinyInteger('approved')->nullable()->default(0);
            $table->tinyInteger('spam')->nullable()->default(0);
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
        Schema::drop('reviews');
    }
}
