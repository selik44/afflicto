<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSeoSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('settings')->insert([
			'category' => 'SEO',
			'type' => 'text',
			'machine' => 'meta_description',
			'name' => 'Meta Description',
			'value' => '',
		]);

		DB::table('settings')->insert([
			'category' => 'SEO',
			'type' => 'text',
			'machine' => 'meta_keywords',
			'name' => 'Meta Keywords',
			'value' => '',
		]);

		Schema::table('products', function(Blueprint $t) {
			$t->string('meta_description', 160);
			$t->text('meta_keywords');
		});

		Schema::table('categories', function(Blueprint $t) {
			$t->string('meta_description', 160);
			$t->text('meta_keywords');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		# delete SEO Settings
		DB::table('settings')->where('machine', '=', 'meta_description')->orWhere('machine', '=', 'meta_keywords')->delete();

		# remove seo fields from products and categories
		Schema::table('products', function(Blueprint $t) {
			$t->dropColumn('meta_description');
			$t->dropColumn('meta_keywords');
		});

		Schema::table('categories', function(Blueprint $t) {
			$t->dropColumn('meta_description');
			$t->dropColumn('meta_keywords');
		});
    }
}
