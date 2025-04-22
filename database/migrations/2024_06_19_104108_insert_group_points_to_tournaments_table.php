<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertGroupPointsToTournamentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->json('group_one_points')->nullable();
            $table->json('group_two_points')->nullable();
            $table->json('group_three_points')->nullable();
            $table->json('group_four_points')->nullable();
            $table->json('group_five_points')->nullable();
            $table->json('group_six_points')->nullable();
            $table->json('group_seven_points')->nullable();
            $table->json('group_eight_points')->nullable();
            $table->json('group_nine_points')->nullable();
            $table->json('group_ten_points')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tournaments', function (Blueprint $table) {
            //
        });
    }
}
