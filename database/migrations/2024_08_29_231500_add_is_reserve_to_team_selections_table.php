<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsReserveToTeamSelectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('team_selections', function (Blueprint $table) {
            $table->boolean('is_reserve')->default(false)->after('team_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('team_selections', function (Blueprint $table) {
            $table->dropColumn('is_reserve');
        });
    }
}
