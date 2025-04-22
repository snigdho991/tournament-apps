<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPreviousRankingToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('previous_ranking')->nullable();
            $table->unsignedBigInteger('current_ranking')->nullable();
            $table->string('move')->nullable();
            $table->unsignedBigInteger('total_points')->nullable();
            $table->unsignedBigInteger('tour_played')->nullable();
            $table->enum('is_current', ['Yes', 'No'])->default('No');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('previous_ranking');
            $table->dropColumn('current_ranking');
            $table->dropColumn('move');
            $table->dropColumn('total_points');
            $table->dropColumn('tour_played');
            $table->dropColumn('is_current');
        });
    }
}
