<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertSomeColsToLeaguesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leagues', function (Blueprint $table) {
            $table->integer('group_size')->nullable();
            $table->integer('player_per_group')->nullable();

            $table->json('round_one_deadline')->nullable();
            $table->json('round_one_matches')->nullable();
            $table->json('round_one_results')->nullable();
            
            $table->json('round_two_deadline')->nullable();
            $table->json('round_two_matches')->nullable();
            $table->json('round_two_results')->nullable();

            $table->json('round_three_deadline')->nullable();
            $table->json('round_three_matches')->nullable();
            $table->json('round_three_results')->nullable();

            $table->json('quarter_final_deadline')->nullable();
            $table->json('quarter_final_matches')->nullable();
            $table->json('quarter_final_results')->nullable();

            $table->json('semi_final_deadline')->nullable();
            $table->json('semi_final_matches')->nullable();
            $table->json('semi_final_results')->nullable();

            $table->json('final_deadline')->nullable();
            $table->json('final_matches')->nullable();
            $table->json('final_results')->nullable();

            $table->json('round_one_winners')->nullable()->after('round_one_results');
            $table->json('round_two_winners')->nullable()->after('round_two_results');
            $table->json('round_three_winners')->nullable()->after('round_three_results');
            $table->json('quarter_final_winners')->nullable()->after('quarter_final_results');
            $table->json('semi_final_winners')->nullable()->after('semi_final_results');
            $table->json('final_winners')->nullable()->after('final_results');

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
            $table->dropColumn('group_size');
            $table->dropColumn('player_per_group');

            $table->dropColumn('round_one_deadline');
            $table->dropColumn('round_one_matches');
            $table->dropColumn('round_one_results');

            $table->dropColumn('round_two_deadline');
            $table->dropColumn('round_two_matches');
            $table->dropColumn('round_two_results');

            $table->dropColumn('round_three_deadline');
            $table->dropColumn('round_three_matches');
            $table->dropColumn('round_three_results');

            $table->dropColumn('quarter_final_deadline');
            $table->dropColumn('quarter_final_matches');
            $table->dropColumn('quarter_final_results');

            $table->dropColumn('semi_final_deadline');
            $table->dropColumn('semi_final_matches');
            $table->dropColumn('semi_final_results');

            $table->dropColumn('final_deadline');
            $table->dropColumn('final_matches');
            $table->dropColumn('final_results');

            $table->dropColumn('round_one_winners');
            $table->dropColumn('round_two_winners');
            $table->dropColumn('round_three_winners');

            $table->dropColumn('quarter_final_winners');
            $table->dropColumn('semi_final_winners');
            $table->dropColumn('final_winners');
        });
    }
}
