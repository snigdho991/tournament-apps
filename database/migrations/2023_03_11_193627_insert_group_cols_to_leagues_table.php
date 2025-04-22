<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertGroupColsToLeaguesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leagues', function (Blueprint $table) {
            $table->json('group_one_players')->nullable();
            $table->json('group_one_deadline')->nullable();
            $table->json('group_one_matches')->nullable();
            $table->longText('group_one_results')->nullable();
            $table->json('group_one_winners')->nullable();

            $table->json('group_two_players')->nullable();
            $table->json('group_two_deadline')->nullable();
            $table->json('group_two_matches')->nullable();
            $table->longText('group_two_results')->nullable();
            $table->json('group_two_winners')->nullable();

            $table->json('group_three_players')->nullable();
            $table->json('group_three_deadline')->nullable();
            $table->json('group_three_matches')->nullable();
            $table->longText('group_three_results')->nullable();
            $table->json('group_three_winners')->nullable();

            $table->json('group_four_players')->nullable();
            $table->json('group_four_deadline')->nullable();
            $table->json('group_four_matches')->nullable();
            $table->longText('group_four_results')->nullable();
            $table->json('group_four_winners')->nullable();

            $table->json('group_five_players')->nullable();
            $table->json('group_five_deadline')->nullable();
            $table->json('group_five_matches')->nullable();
            $table->longText('group_five_results')->nullable();
            $table->json('group_five_winners')->nullable();
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
