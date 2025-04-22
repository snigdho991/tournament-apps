<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertSixTillTenGroupColsToLeaguesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leagues', function (Blueprint $table) {
            $table->json('group_six_players')->nullable();
            $table->json('group_six_deadline')->nullable();
            $table->json('group_six_matches')->nullable();
            $table->longText('group_six_results')->nullable();
            $table->json('group_six_winners')->nullable();

            $table->json('group_seven_players')->nullable();
            $table->json('group_seven_deadline')->nullable();
            $table->json('group_seven_matches')->nullable();
            $table->longText('group_seven_results')->nullable();
            $table->json('group_seven_winners')->nullable();

            $table->json('group_eight_players')->nullable();
            $table->json('group_eight_deadline')->nullable();
            $table->json('group_eight_matches')->nullable();
            $table->longText('group_eight_results')->nullable();
            $table->json('group_eight_winners')->nullable();

            $table->json('group_nine_players')->nullable();
            $table->json('group_nine_deadline')->nullable();
            $table->json('group_nine_matches')->nullable();
            $table->longText('group_nine_results')->nullable();
            $table->json('group_nine_winners')->nullable();

            $table->json('group_ten_players')->nullable();
            $table->json('group_ten_deadline')->nullable();
            $table->json('group_ten_matches')->nullable();
            $table->longText('group_ten_results')->nullable();
            $table->json('group_ten_winners')->nullable();
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
