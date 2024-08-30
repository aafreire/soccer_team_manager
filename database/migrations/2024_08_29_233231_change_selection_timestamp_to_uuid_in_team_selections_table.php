<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSelectionTimestampToUuidInTeamSelectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('team_selections', function (Blueprint $table) {
            // Remover a coluna antiga
            $table->dropColumn('selection_timestamp');

            // Adicionar uma nova coluna UUID
            $table->uuid('selection_id')->after('game_date')->index();
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
            // Adicionar novamente a coluna antiga (caso queira reverter)
            $table->timestamp('selection_timestamp')->after('game_date');

            // Remover a nova coluna
            $table->dropColumn('selection_id');
        });
    }
}
