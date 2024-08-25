<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayersTable extends Migration
{
    public function up()
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->tinyInteger('level')->comment('Player skill level from 1 to 5');
            $table->boolean('is_goalkeeper')->default(false)->comment('Is the player a goalkeeper?');
            $table->boolean('is_present')->default(false)->comment('Has the player confirmed attendance?');
            $table->boolean('is_deleted')->default(false)->comment('Is the player logically deleted?');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('players');
    }
};
