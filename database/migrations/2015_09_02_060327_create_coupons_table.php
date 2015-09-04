<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function(Blueprint $t) {
			$t->increments('id');
			$t->timestamps();
			$t->softDeletes();
			$t->string('admin_name');		# administrative name
			$t->string('name');				# name: shown in receipts etc
			$t->string('code');				# the code
			$t->float('discount');			# discount, from 0 to 100
			$t->text('categories');			# apply discount on all products in a given category
			$t->text('products');			# apply discount on the given products
			$t->date('valid_until')			# the date at which the coupon is no longer valid
				->nullable();
			$t->boolean('free_shipping');	# apply free shipping?
			$t->boolean('cumulative');		# combine this discount with tag discounts?
			$t->boolean('enabled');			# whether the coupon is enabled or not
		});

		Schema::create('coupon_user', function(Blueprint $t) {
			$t->increments('id');
			$t->integer('user_id')->unsigned();
			$t->integer('coupon_id')->unsigned();

			$t->foreign('user_id')
				->references('id')->on('users')
				->onUpdate('cascade')
				->onDelete('cascade');

			$t->foreign('coupon_id')
				->references('id')->on('coupons')
				->onUpdate('cascade')
				->onDelete('cascade');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::dropIfExists('coupon_user');
        Schema::dropIfExists('coupons');
    }
}
