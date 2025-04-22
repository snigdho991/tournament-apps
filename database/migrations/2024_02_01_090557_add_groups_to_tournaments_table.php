<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGroupsToTournamentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->integer('group_size')->nullable();
            $table->integer('player_per_group')->nullable();
            
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

            $table->longText('group_one_stats')->nullable();
            $table->longText('group_two_stats')->nullable();
            $table->longText('group_three_stats')->nullable();
            $table->longText('group_four_stats')->nullable();
            $table->longText('group_five_stats')->nullable();
            $table->longText('group_six_stats')->nullable();
            $table->longText('group_seven_stats')->nullable();
            $table->longText('group_eight_stats')->nullable();
            $table->longText('group_nine_stats')->nullable();
            $table->longText('group_ten_stats')->nullable();

            $table->json('group_one_status')->nullable()->after('group_one_results');
            $table->json('group_two_status')->nullable()->after('group_two_results');
            $table->json('group_three_status')->nullable()->after('group_three_results');
            $table->json('group_four_status')->nullable()->after('group_four_results');
            $table->json('group_five_status')->nullable()->after('group_five_results');

            $table->json('group_one_retires')->nullable()->after('group_one_results');
            $table->json('group_two_retires')->nullable()->after('group_two_results');
            $table->json('group_three_retires')->nullable()->after('group_three_results');
            $table->json('group_four_retires')->nullable()->after('group_four_results');
            $table->json('group_five_retires')->nullable()->after('group_five_results');

            $table->json('group_six_status')->nullable()->after('group_six_results');
            $table->json('group_seven_status')->nullable()->after('group_seven_results');
            $table->json('group_eight_status')->nullable()->after('group_eight_results');
            $table->json('group_nine_status')->nullable()->after('group_nine_results');
            $table->json('group_ten_status')->nullable()->after('group_ten_results');

            $table->json('group_six_retires')->nullable()->after('group_six_results');
            $table->json('group_seven_retires')->nullable()->after('group_seven_results');
            $table->json('group_eight_retires')->nullable()->after('group_eight_results');
            $table->json('group_nine_retires')->nullable()->after('group_nine_results');
            $table->json('group_ten_retires')->nullable()->after('group_ten_results');
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
