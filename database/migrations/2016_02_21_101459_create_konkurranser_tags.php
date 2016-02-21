<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKonkurranserTags extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('tags')->insert([
	        'label' => 'Konkurranser Venstere',
	        'color' => '#000000',
	        'enabled' => 1,
	        'type' => 'contest_left',
	        'visible' => 0,
	        'discount' => 0,
        ]);

	    DB::table('tags')->insert([
		    'label' => 'Konkurranser Høyere',
		    'color' => '#000000',
		    'enabled' => 1,
		    'type' => 'contest_right',
		    'visible' => 0,
		    'discount' => 0,
	    ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('tags')->where('type', '=', 'contest_left')->delete();
	    DB::table('tags')->where('type', '=', 'contest_right')->delete();
    }
}
