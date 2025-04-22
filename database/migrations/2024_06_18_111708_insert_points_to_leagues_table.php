<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertPointsToLeaguesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leagues', function (Blueprint $table) {
            $table->json('round_one_points')->nullable()->after('round_one_results');
            $table->json('round_two_points')->nullable()->after('round_two_results');
            $table->json('round_three_points')->nullable()->after('round_three_results');
            $table->json('quarter_final_points')->nullable()->after('quarter_final_results');
            $table->json('semi_final_points')->nullable()->after('semi_final_results');
            $table->json('final_points')->nullable()->after('final_results');

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
        Schema::table('leagues', function (Blueprint $table) {
            //
        });
    }
}
