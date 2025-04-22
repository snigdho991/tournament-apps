<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertSomeColsToTournamentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tournaments', function (Blueprint $table) {
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
        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropColumn('round_two_winners');
            $table->dropColumn('round_three_winners');
            $table->dropColumn('quarter_final_winners');
            $table->dropColumn('semi_final_winners');
            $table->dropColumn('final_winners');
        });
    }
}
