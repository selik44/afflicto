<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RolesRenameAdminToSuperadmin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		DB::table('roles')->where('machine', '=', 'admin')->update(['machine' => 'superadmin']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		DB::table('roles')->where('machine', '=', 'superadmin')->update(['machine' => 'admin']);
    }
}
