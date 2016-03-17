<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMissingPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		DB::table('permissions')->where('machine', '=', 'receival.view')->delete();
		DB::table('permissions')->where('machine', '=', 'receival.edit')->delete();
		DB::table('permissions')->where('machine', '=', 'receival.create')->delete();
		DB::table('permissions')->where('machine', '=', 'receival.delete')->delete();

		$perms = [
			'slides.view' => 'View Slides',
			'slides.edit' => 'Edit Slides',
			'slides.create' => 'Create Slides',
			'slides.delete' => 'Delete Slides',

			'banners.view' => 'View Banners',
			'banners.edit' => 'Edit Banners',
			'banners.create' => 'Create Banners',
			'banners.delete' => 'Delete Banners',

			'coupons.view' => 'View Coupons',
			'coupons.edit' => 'Edit Coupons',
			'coupons.create' => 'Create Coupons',
			'coupons.delete' => 'Delete Coupons',

			'pages.view' => 'View Pages',
			'pages.edit' => 'Edit Pages',
			'pages.create' => 'Create Pages',
			'pages.delete' => 'Delete Pages',

			'variants.view' => 'View Variants',
			'variants.edit' => 'Edit Variants',
			'variants.create' => 'Create Variants',
			'variants.delete' => 'Delete Variants',

			'tags.view' => 'View Tags',
			'tags.edit' => 'Edit Tags',
			'tags.create' => 'Create Tags',
			'tags.delete' => 'Delete Tags',
		];

		foreach($perms as $machine => $name) {
			DB::table('permissions')->insert([
				'machine' => $machine,
				'name' => $name,
			]);
		}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
